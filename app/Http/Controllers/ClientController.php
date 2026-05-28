<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function index(): View
    {
        $mesClients = auth()->user()->clients;
        return view('clients.index', compact('mesClients'));
    }

    public function create(): View
    {
        return view('clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom' => 'required_without:raison_sociale|nullable|string|max:255',
            'raison_sociale' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $client = new Client();
        $client->id_utilisateur = auth()->id();

        $client->nom = $request->input('nom');
        $client->prenom = $request->input('prenom');
        $client->raison_sociale = $request->input('raison_sociale');
        $client->adresse = $request->input('adresse');
        $client->code_postal = $request->input('code_postal');
        $client->ville = $request->input('ville');
        $client->siret = $request->input('siret');
        $client->email = $request->input('email');
        $client->telephone = $request->input('telephone');

        $client->save();

        return redirect()->route('clients.index')->with('success', 'Client créé avec succès.');
    }

    // --- MISE À JOUR ICI ---
    public function show(string $id): View
    {
        // 1. Récupérer le client et ses documents (triés par date décroissante)
        $client = Client::where('id_client', $id)
            ->where('id_utilisateur', auth()->id())
            ->with(['documents' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->firstOrFail();

        // 2. Calculer le total des achats (Factures validées/payées uniquement)
        $totalAchat = $client->documents
            ->where('type_document', 'Facture')
            ->whereIn('statut', ['Valide', 'Paye', 'Accepte'])
            ->sum('total_ttc');

        return view('clients.show', compact('client', 'totalAchat'));
    }
    // -----------------------

    public function edit(string $id): View
    {
        $client = Client::where('id_client', $id)
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();

        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $client = Client::where('id_client', $id)
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();

        $request->validate([
            'nom' => 'required_without:raison_sociale|nullable|string|max:255',
            'raison_sociale' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $client->nom = $request->input('nom');
        $client->prenom = $request->input('prenom');
        $client->raison_sociale = $request->input('raison_sociale');
        $client->adresse = $request->input('adresse');
        $client->code_postal = $request->input('code_postal');
        $client->ville = $request->input('ville');
        $client->siret = $request->input('siret');
        $client->email = $request->input('email');
        $client->telephone = $request->input('telephone');

        $client->save();

        return redirect()->route('clients.index')->with('success', 'Client mis à jour.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $client = Client::where('id_client', $id)
            ->where('id_utilisateur', auth()->id())
            ->firstOrFail();

        // On nettoie les logos des documents associés avant de supprimer
        foreach ($client->documents as $document) {
            if ($document->logo_path) {
                Storage::disk('public')->delete($document->logo_path);
            }
        }

        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');

        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
            fgetcsv($handle, 1000, ';'); // Skip header

            while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                if (empty($data[0]) && empty($data[2])) continue;

                $clientData = [
                    'id_utilisateur' => auth()->id(),
                    'nom'            => $data[0] ?? null,
                    'prenom'         => $data[1] ?? null,
                    'raison_sociale' => $data[2] ?? null,
                    'email'          => $data[3] ?? null,
                    'telephone'      => $data[4] ?? null,
                    'adresse'        => $data[5] ?? null,
                    'code_postal'    => $data[6] ?? null,
                    'ville'          => $data[7] ?? null,
                    'siret'          => $data[8] ?? null,
                ];

                if (!empty($data[3])) {
                    Client::updateOrCreate(
                        ['email' => $data[3], 'id_utilisateur' => auth()->id()],
                        $clientData
                    );
                } else {
                    Client::create($clientData);
                }
            }
            fclose($handle);
        }

        return back()->with('success', 'Clients importés avec succès !');
    }
}
