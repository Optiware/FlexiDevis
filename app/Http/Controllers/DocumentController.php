<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Document;
use App\Models\Product;
use App\Services\CalculDocument;
use App\Services\Numerotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Document::where('id_utilisateur', auth()->id())->with('client');

        if ($request->filled('client_id')) {
            $query->where('id_client', $request->client_id);
        }

        $documents = $query->orderBy('created_at', 'desc')->get();

        $clients = Client::where('id_utilisateur', auth()->id())
            ->orderBy('raison_sociale')
            ->get();

        return view('documents.index', compact('documents', 'clients'));
    }

    public function create(Request $request): View
    {
        $clients = Client::where('id_utilisateur', auth()->id())->get();

        $products = Product::where('id_utilisateur', auth()->id())
            ->orderBy('designation')
            ->get();

        $typeParDefaut = $request->query('type') === 'Facture' ? 'Facture' : 'Devis';
        $prochainNumero = Numerotation::suivant(auth()->id(), $typeParDefaut);

        return view('documents.create', compact('clients', 'products', 'typeParDefaut', 'prochainNumero'));
    }

    public function store(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'id_client' => ['required', $this->clientDuCompte()],
            'type_document' => 'required|in:Devis,Facture',
            'date_emission' => 'required|date',
            'date_echeance' => 'nullable|date|after_or_equal:date_emission',
            'numero' => [
                'required', 'string', 'max:50',
                Rule::unique('documents', 'numero')->where('id_utilisateur', auth()->id()),
            ],
            'notes' => 'nullable|string',
            'design_template' => 'nullable|in:classic,minimal,bold,modern',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ...$this->reglesMontants(),
        ]);

        DB::transaction(function () use ($request, $donnees) {
            $document = new Document();
            $document->id_utilisateur = auth()->id();
            $document->type_document = $donnees['type_document'];
            $document->numero = $donnees['numero'];
            $document->statut = 'Brouillon';
            $this->remplir($document, $request, $donnees);
        });

        return redirect()->route('documents.index')->with('success', 'Document créé avec succès !');
    }

    public function show(string $id): View
    {
        $document = $this->documentDuCompte($id, ['lignes', 'client']);

        return view('documents.show', compact('document'));
    }

    public function edit(string $id): View
    {
        $document = $this->documentDuCompte($id, ['lignes']);

        $clients = Client::where('id_utilisateur', auth()->id())->get();

        $products = Product::where('id_utilisateur', auth()->id())
            ->orderBy('designation')
            ->get();

        return view('documents.edit', compact('document', 'clients', 'products'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $document = $this->documentDuCompte($id);

        $donnees = $request->validate([
            'id_client' => ['required', $this->clientDuCompte()],
            'date_emission' => 'required|date',
            'date_echeance' => 'nullable|date|after_or_equal:date_emission',
            'statut' => 'required|in:Brouillon,Valide,Accepte,Refuse,Paye,Impaye',
            'notes' => 'nullable|string',
            'design_template' => 'nullable|in:classic,minimal,bold,modern',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ...$this->reglesMontants(),
        ]);

        DB::transaction(function () use ($request, $donnees, $document) {
            $document->statut = $donnees['statut'];
            $document->lignes()->delete();
            $this->remplir($document, $request, $donnees);
        });

        return redirect()->route('documents.index')->with('success', 'Document mis à jour avec succès !');
    }

    public function duplicate(string $id): RedirectResponse
    {
        $original = $this->documentDuCompte($id, ['lignes']);

        $copie = DB::transaction(function () use ($original) {
            $copie = $original->replicate();
            $copie->numero = Numerotation::suivant(auth()->id(), $original->type_document);
            $copie->statut = 'Brouillon';
            $copie->date_emission = now()->format('Y-m-d');
            $copie->date_echeance = now()->addDays(30)->format('Y-m-d');

            // La copie a son propre fichier de logo : supprimer l'un n'efface pas l'autre.
            if ($original->logo_path && Storage::disk('public')->exists($original->logo_path)) {
                $nouveau = 'logos/'.uniqid('copie_').'.'.pathinfo($original->logo_path, PATHINFO_EXTENSION);
                Storage::disk('public')->copy($original->logo_path, $nouveau);
                $copie->logo_path = $nouveau;
            }
            $copie->save();

            foreach ($original->lignes as $ligne) {
                $copie->lignes()->create($ligne->only([
                    'ordre', 'description', 'quantite', 'prix_unitaire', 'taux_tva',
                    'remise_percent', 'montant_ht', 'montant_tva', 'montant_ttc',
                ]));
            }

            return $copie;
        });

        return redirect()->route('documents.edit', $copie->id_document)
            ->with('success', 'Document dupliqué avec succès !');
    }

    public function destroy(string $id): RedirectResponse
    {
        $document = $this->documentDuCompte($id);
        $logo = $document->logo_path;

        $document->delete();
        self::supprimerLogoSiInutilise($logo);

        return redirect()->route('documents.index')->with('success', 'Document supprimé.');
    }

    public function download(string $id)
    {
        $document = $this->documentDuCompte($id, ['lignes', 'client']);

        $pdf = Pdf::loadView('pdf.document', compact('document'));
        $fileName = $document->type_document.'-'.$document->numero.'.pdf';

        return $pdf->download($fileName);
    }

    /** Supprime un logo seulement si plus aucun document ne l'utilise. */
    public static function supprimerLogoSiInutilise(?string $chemin): void
    {
        if ($chemin && ! Document::where('logo_path', $chemin)->exists()) {
            Storage::disk('public')->delete($chemin);
        }
    }

    // ——— Outils ———

    private function documentDuCompte(string $id, array $relations = []): Document
    {
        return Document::with($relations)
            ->where('id_document', $id)
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();
    }

    /** Le client doit exister ET appartenir au compte connecté. */
    private function clientDuCompte()
    {
        return Rule::exists('clients', 'id_client')->where('id_utilisateur', auth()->id());
    }

    private function reglesMontants(): array
    {
        return [
            'remise_globale' => 'nullable|numeric|min:0|max:100',
            'lignes' => 'nullable|array',
            'lignes.*.description' => 'nullable|string|max:1000',
            'lignes.*.quantite' => 'required|numeric|min:0',
            'lignes.*.prix_unitaire' => 'required|numeric|min:0',
            'lignes.*.tva' => 'nullable|numeric|min:0|max:100',
            'lignes.*.remise_percent' => 'nullable|numeric|min:0|max:100',
        ];
    }

    /** Champs communs à la création et à la modification, lignes et totaux compris. */
    private function remplir(Document $document, Request $request, array $donnees): void
    {
        $document->id_client = $donnees['id_client'];
        $document->date_emission = $donnees['date_emission'];
        $document->date_echeance = $donnees['date_echeance'] ?? null;
        $document->notes = $donnees['notes'] ?? null;
        $document->remise_globale = $donnees['remise_globale'] ?? 0;
        $document->design_template = $donnees['design_template'] ?? 'classic';

        if ($request->hasFile('logo')) {
            $ancien = $document->logo_path;
            $document->logo_path = $request->file('logo')->store('logos', 'public');
            $document->save();
            self::supprimerLogoSiInutilise($ancien);
        }

        $calcul = CalculDocument::calculer($donnees['lignes'] ?? [], (float) $document->remise_globale);
        $document->total_ht = $calcul['total_ht'];
        $document->total_tva = $calcul['total_tva'];
        $document->total_ttc = $calcul['total_ttc'];
        $document->save();

        foreach ($calcul['lignes'] as $ligne) {
            $document->lignes()->create($ligne);
        }
    }
}
