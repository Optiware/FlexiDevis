<?php

use App\Models\Client;
use App\Models\Document;
use App\Models\User;

it('affiche le tableau de bord avec le chiffre d’affaires du mois', function () {
    $user = User::factory()->create();
    $client = Client::create(['id_utilisateur' => $user->getKey(), 'nom' => 'Durand']);
    foreach ([['F-1', 'Paye', 300], ['F-2', 'Valide', 200], ['F-3', 'Brouillon', 999]] as [$numero, $statut, $ht]) {
        Document::create([
            'id_client' => $client->id_client,
            'id_utilisateur' => $user->getKey(),
            'type_document' => 'Facture',
            'numero' => $numero,
            'date_emission' => now()->startOfMonth()->toDateString(),
            'statut' => $statut,
            'total_ht' => $ht,
            'total_tva' => 0,
            'total_ttc' => $ht,
        ]);
    }

    $reponse = $this->actingAs($user)->get(route('dashboard'))->assertOk();

    $graphique = $reponse->viewData('dataGraphique');
    expect($graphique)->toHaveCount(12)
        ->and($graphique[now()->month - 1])->toEqual(500)
        ->and((float) $reponse->viewData('caTotal'))->toBe(500.0);
});
