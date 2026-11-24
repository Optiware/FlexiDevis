<?php

use App\Models\Client;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function clientDe(User $user, string $nom = 'Dupont'): Client
{
    return Client::create(['id_utilisateur' => $user->getKey(), 'nom' => $nom, 'raison_sociale' => "$nom SARL"]);
}

function devis(Client $client, array $champs = []): array
{
    return array_merge([
        'id_client' => $client->id_client,
        'type_document' => 'Devis',
        'date_emission' => '2026-11-24',
        'numero' => 'D-2026-0001',
        'remise_globale' => 10,
        'lignes' => [
            ['description' => 'Pose carrelage', 'quantite' => 10, 'prix_unitaire' => 45, 'tva' => 10, 'remise_percent' => 0],
            ['description' => 'Fournitures', 'quantite' => 1, 'prix_unitaire' => 50, 'tva' => 20],
        ],
    ], $champs);
}

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->client = clientDe($this->user);
});

it('crée un devis avec ses lignes et des totaux cohérents', function () {
    $this->actingAs($this->user)
        ->post(route('documents.store'), devis($this->client))
        ->assertRedirect(route('documents.index'))
        ->assertSessionHasNoErrors();

    $document = Document::with('lignes')->sole();
    // Lignes : 450 HT (TVA 45) + 50 HT (TVA 10) ; remise globale 10 % sur le HT puis la TVA.
    expect((float) $document->total_ht)->toBe(450.0)
        ->and((float) $document->total_tva)->toBe(49.5)
        ->and((float) $document->total_ttc)->toBe(499.5)
        ->and($document->statut)->toBe('Brouillon')
        ->and($document->lignes->pluck('ordre')->all())->toBe([1, 2]);
});

it('refuse le client d’un autre compte', function () {
    $autre = clientDe(User::factory()->create(), 'Martin');

    $this->actingAs($this->user)
        ->post(route('documents.store'), devis($autre))
        ->assertSessionHasErrors('id_client');

    expect(Document::count())->toBe(0);
});

it('refuse des montants impossibles', function () {
    $this->actingAs($this->user)
        ->post(route('documents.store'), devis($this->client, [
            'remise_globale' => 150,
            'lignes' => [['description' => 'X', 'quantite' => -1, 'prix_unitaire' => 10]],
        ]))
        ->assertSessionHasErrors(['remise_globale', 'lignes.0.quantite']);
});

it('numérote devis et factures séparément, sans doublon', function () {
    $this->actingAs($this->user)->post(route('documents.store'), devis($this->client));

    // Après D-2026-0001 vient D-2026-0002 ; les factures ont leur propre suite.
    $this->get(route('documents.create'))->assertSee('D-2026-0002');
    $this->get(route('documents.create', ['type' => 'Facture']))->assertSee('F-2026-0001');

    // Même numéro une deuxième fois : refusé.
    $this->post(route('documents.store'), devis($this->client))->assertSessionHasErrors('numero');
});

it('laisse deux comptes avoir chacun leur D-2026-0001', function () {
    $autre = User::factory()->create();

    $this->actingAs($this->user)->post(route('documents.store'), devis($this->client));
    $this->actingAs($autre)->post(route('documents.store'), devis(clientDe($autre, 'Martin')))
        ->assertSessionHasNoErrors();

    expect(Document::where('numero', 'D-2026-0001')->count())->toBe(2);
});

it('cache les documents des autres comptes', function () {
    $this->actingAs($this->user)->post(route('documents.store'), devis($this->client));
    $document = Document::sole();

    $this->actingAs(User::factory()->create());
    $this->get(route('documents.show', $document->id_document))->assertNotFound();
    $this->get(route('documents.download', $document->id_document))->assertNotFound();
    $this->delete(route('documents.destroy', $document->id_document))->assertNotFound();
    expect(Document::count())->toBe(1);
});

it('met à jour un document et recalcule ses totaux', function () {
    $this->actingAs($this->user)->post(route('documents.store'), devis($this->client));
    $document = Document::sole();

    $this->put(route('documents.update', $document->id_document), [
        'id_client' => $this->client->id_client,
        'date_emission' => '2026-11-25',
        'statut' => 'Valide',
        'remise_globale' => 0,
        'lignes' => [['description' => 'Forfait', 'quantite' => 1, 'prix_unitaire' => 100, 'tva' => 20]],
    ])->assertSessionHasNoErrors();

    $document->refresh()->load('lignes');
    expect($document->statut)->toBe('Valide')
        ->and((float) $document->total_ttc)->toBe(120.0)
        ->and($document->lignes)->toHaveCount(1);
});

it('duplique un devis avec un nouveau numéro et son propre logo', function () {
    Storage::fake('public');
    $this->actingAs($this->user)->post(route('documents.store'), devis($this->client, [
        // Vraie image PNG de 2 × 2 pixels (sans dépendre de l'extension GD).
        'logo' => UploadedFile::fake()->createWithContent('logo.png', base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAIAAAD91JpzAAAAEUlEQVR4nGP4z8DwnwGMgRQAH+4D/dJQfRoAAAAASUVORK5CYII='
        )),
    ]));
    $original = Document::sole();

    $this->post(route('documents.duplicate', $original->id_document))->assertRedirect();
    $copie = Document::where('id_document', '!=', $original->id_document)->sole();

    expect($copie->numero)->toBe('D-'.date('Y').'-0002')
        ->and($copie->statut)->toBe('Brouillon')
        ->and($copie->lignes()->count())->toBe(2)
        ->and($copie->logo_path)->not->toBe($original->logo_path);

    // Supprimer la copie ne doit pas effacer le logo de l'original.
    $this->delete(route('documents.destroy', $copie->id_document));
    Storage::disk('public')->assertExists($original->logo_path);
    Storage::disk('public')->assertMissing($copie->logo_path);
});

it('télécharge le document en PDF', function () {
    $this->actingAs($this->user)->post(route('documents.store'), devis($this->client));
    $document = Document::sole();

    $reponse = $this->get(route('documents.download', $document->id_document))->assertOk();
    expect($reponse->headers->get('content-type'))->toBe('application/pdf')
        ->and($reponse->headers->get('content-disposition'))->toContain('Devis-D-2026-0001.pdf');
});


it('charge les données de démonstration sans doublon', function () {
    $this->seed();
    $this->seed();

    $alexis = User::where('email', 'alexis@flexidevis.com')->sole();
    expect($alexis->raison_sociale)->toBe('FlexiDevis Studio')
        ->and($alexis->documents()->count())->toBe(3)
        ->and($alexis->clients()->count())->toBe(3);
    $document = $alexis->documents()->where('remise_globale', '>', 0)->sole();
    expect((float) $document->total_ht + (float) $document->total_tva)->toBe((float) $document->total_ttc);
});
