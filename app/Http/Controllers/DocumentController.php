<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\LigneDocument;

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

        $typeParDefaut = $request->query('type', 'Devis');

        $anneeCourante = date('Y');
        $nombreDocuments = Document::where('id_utilisateur', auth()->id())
            ->whereYear('created_at', $anneeCourante)
            ->latest('created_at')
            ->first();

        if ($nombreDocuments) {
            $parties = explode('-', $nombreDocuments->numero);
            $sequence = intval(end($parties)) + 1;
        } else {
            $sequence = 1;
        }

        $prefixe = ($typeParDefaut === 'Devis') ? 'D' : 'F';
        $prochainNumero = sprintf('%s-%s-%04d', $prefixe, $anneeCourante, $sequence);

        return view('documents.create', compact('clients', 'products', 'typeParDefaut', 'prochainNumero'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'id_client' => 'required|exists:clients,id_client',
            'type_document' => 'required|in:Devis,Facture',
            'date_emission' => 'required|date',
            'numero' => 'required|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $document = new Document();
        $document->id_utilisateur = auth()->id();
        $document->id_client = $request->input('id_client');
        $document->type_document = $request->input('type_document');
        $document->numero = $request->input('numero');
        $document->date_emission = $request->input('date_emission');
        $document->date_echeance = $request->input('date_echeance');
        $document->statut = 'Brouillon';
        $document->notes = $request->input('notes');
        $document->remise_globale = $request->input('remise_globale', 0);
        $document->design_template = $request->input('design_template', 'classic');

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $document->logo_path = $path;
        }

        $document->total_ht = 0;
        $document->total_tva = 0;
        $document->total_ttc = 0;
        $document->save();

        $globalTotalHT = 0;
        $globalTotalTVA = 0;

        if ($request->has('lignes')) {
            foreach ($request->input('lignes') as $ligne) {
                $quantite = $ligne['quantite'] ?? 0;
                $prixUnitaire = $ligne['prix_unitaire'] ?? 0;
                $tauxTva = $ligne['tva'] ?? 20;
                $remise = $ligne['remise_percent'] ?? 0;

                $montantBrut = $quantite * $prixUnitaire;
                $ligneHt = $montantBrut - ($montantBrut * ($remise / 100));
                $ligneTva = $ligneHt * ($tauxTva / 100);
                $ligneTtc = $ligneHt + $ligneTva;

                $globalTotalHT += $ligneHt;
                $globalTotalTVA += $ligneTva;

                $document->lignes()->create([
                    'description'   => $ligne['description'] ?? 'Sans titre',
                    'quantite'      => $quantite,
                    'prix_unitaire' => $prixUnitaire,
                    'taux_tva'      => $tauxTva,
                    'remise_percent'=> $remise,
                    'montant_ht'    => $ligneHt,
                    'montant_tva'   => $ligneTva,
                    'montant_ttc'   => $ligneTtc,
                ]);
            }
        }

        $globalTotalBrutTTC = $globalTotalHT + $globalTotalTVA;
        $globalTotalTTC = $globalTotalBrutTTC - ($globalTotalBrutTTC * ($document->remise_globale / 100));

        $document->total_ht = $globalTotalHT;
        $document->total_tva = $globalTotalTVA;
        $document->total_ttc = $globalTotalTTC;
        $document->save();

        return redirect()->route('documents.index')->with('success', 'Document créé avec succès !');
    }

    public function show(string $id): View
    {
        $document = Document::with(['lignes', 'client'])
            ->where('id_document', $id)
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();

        return view('documents.show', compact('document'));
    }

    public function edit(string $id): View
    {
        $document = Document::where('id_document', $id)
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();

        $clients = Client::where('id_utilisateur', auth()->id())->get();

        $products = Product::where('id_utilisateur', auth()->id())
            ->orderBy('designation')
            ->get();

        return view('documents.edit', compact('document', 'clients', 'products'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $document = Document::where('id_document', $id)
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();

        $request->validate([
            'id_client' => 'required|exists:clients,id_client',
            'date_emission' => 'required|date',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'statut' => 'required|in:Brouillon,Valide,Accepte,Refuse,Paye,Impaye',
            'notes' => 'nullable|string',
        ]);

        $document->id_client = $request->input('id_client');
        $document->date_emission = $request->input('date_emission');
        $document->date_echeance = $request->input('date_echeance');
        $document->statut = $request->input('statut', 'Brouillon');
        $document->notes = $request->input('notes');
        $document->remise_globale = $request->input('remise_globale', 0);
        $document->design_template = $request->input('design_template', 'classic');

        if ($request->hasFile('logo')) {
            if ($document->logo_path) {
                Storage::disk('public')->delete($document->logo_path);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $document->logo_path = $path;
        }

        $document->lignes()->delete();

        $globalTotalHT = 0;
        $globalTotalTVA = 0;

        if ($request->has('lignes')) {
            foreach ($request->input('lignes') as $ligne) {
                $quantite = $ligne['quantite'] ?? 0;
                $prixUnitaire = $ligne['prix_unitaire'] ?? 0;
                $tauxTva = $ligne['tva'] ?? 20;
                $remise = $ligne['remise_percent'] ?? 0;

                $montantBrut = $quantite * $prixUnitaire;
                $ligneHt = $montantBrut - ($montantBrut * ($remise / 100));
                $ligneTva = $ligneHt * ($tauxTva / 100);
                $ligneTtc = $ligneHt + $ligneTva;

                $globalTotalHT += $ligneHt;
                $globalTotalTVA += $ligneTva;

                $document->lignes()->create([
                    'description'   => $ligne['description'] ?? 'Sans titre',
                    'quantite'      => $quantite,
                    'prix_unitaire' => $prixUnitaire,
                    'taux_tva'      => $tauxTva,
                    'remise_percent'=> $remise,
                    'montant_ht'    => $ligneHt,
                    'montant_tva'   => $ligneTva,
                    'montant_ttc'   => $ligneTtc,
                ]);
            }
        }

        $globalTotalBrutTTC = $globalTotalHT + $globalTotalTVA;
        $globalTotalTTC = $globalTotalBrutTTC - ($globalTotalBrutTTC * ($document->remise_globale / 100));

        $document->total_ht = $globalTotalHT;
        $document->total_tva = $globalTotalTVA;
        $document->total_ttc = $globalTotalTTC;

        $document->statut = $request->input('statut');
        $document->save();

        return redirect()->route('documents.index')->with('success', 'Document mis à jour avec succès !');
    }

    public function duplicate(string $id): RedirectResponse
    {
        $originalDocument = Document::with('lignes')->where('id_document', $id)
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();

        $anneeCourante = date('Y');
        $dernierDoc = Document::where('id_utilisateur', auth()->id())
            ->whereYear('created_at', $anneeCourante)
            ->latest('created_at')
            ->first();

        if ($dernierDoc) {
            $parties = explode('-', $dernierDoc->numero);
            $sequence = intval(end($parties)) + 1;
        } else {
            $sequence = 1;
        }

        $prefixe = ($originalDocument->type_document === 'Devis') ? 'D' : 'F';
        $nouveauNumero = sprintf('%s-%s-%04d', $prefixe, $anneeCourante, $sequence);

        $nouveauDocument = $originalDocument->replicate();
        $nouveauDocument->numero = $nouveauNumero;
        $nouveauDocument->statut = 'Brouillon';
        $nouveauDocument->date_emission = now()->format('Y-m-d');
        $nouveauDocument->date_echeance = now()->addDays(30)->format('Y-m-d');
        $nouveauDocument->created_at = now();
        $nouveauDocument->updated_at = now();
        $nouveauDocument->save();

        foreach ($originalDocument->lignes as $ligne) {
            $nouveauDocument->lignes()->create([
                'description'   => $ligne->description,
                'quantite'      => $ligne->quantite,
                'prix_unitaire' => $ligne->prix_unitaire,
                'taux_tva'      => $ligne->taux_tva,
                'remise_percent'=> $ligne->remise_percent,
                'montant_ht'    => $ligne->montant_ht,
                'montant_tva'   => $ligne->montant_tva,
                'montant_ttc'   => $ligne->montant_ttc,
            ]);
        }

        return redirect()->route('documents.edit', $nouveauDocument->id_document)
            ->with('success', 'Document dupliqué avec succès !');
    }

    public function destroy(string $id): RedirectResponse
    {
        $document = Document::where('id_document', $id)
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();

        if ($document->logo_path) {
            Storage::disk('public')->delete($document->logo_path);
        }

        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Document supprimé.');
    }

    public function download($id)
    {
        $document = Document::with(['lignes', 'client'])
            ->where('id_document', $id)
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.document', compact('document'));
        $fileName = $document->type_document . '-' . $document->numero . '.pdf';

        return $pdf->download($fileName);
    }
}
