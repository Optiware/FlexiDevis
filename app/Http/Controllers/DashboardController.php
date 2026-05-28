<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $clientId = $request->input('client_id');

        $clients = Client::where('id_utilisateur', $userId)
            ->orderBy('nom')
            ->orderBy('raison_sociale')
            ->get();

        $queryCA = Document::where('id_utilisateur', $userId)
            ->where('type_document', 'Facture')
            ->whereIn('statut', ['Valide', 'Paye']);

        if ($clientId) {
            $queryCA->where('id_client', $clientId);
        }

        $caTotal = $queryCA->sum('total_ht');

        $queryDevis = Document::where('id_utilisateur', $userId)
            ->where('type_document', 'Devis')
            ->where('statut', 'Brouillon');

        if ($clientId) {
            $queryDevis->where('id_client', $clientId);
        }

        $devisEnAttente = $queryDevis->count();

        $queryTopClients = Client::where('id_utilisateur', $userId)
            ->withSum(['documents' => function ($query) {
                $query->where('type_document', 'Facture')
                    ->whereIn('statut', ['Valide', 'Paye']);
            }], 'total_ht');

        if ($clientId) {
            $queryTopClients->where('id_client', $clientId);
        }

        $topClients = $queryTopClients->orderByDesc('documents_sum_total_ht')
            ->take(5)
            ->get();

        $queryGraph = Document::where('id_utilisateur', $userId)
            ->where('type_document', 'Facture')
            ->whereIn('statut', ['Valide', 'Paye'])
            ->whereYear('date_emission', date('Y'));

        if ($clientId) {
            $queryGraph->where('id_client', $clientId);
        }

        $ventesParMois = $queryGraph->selectRaw('MONTH(date_emission) as mois, SUM(total_ht) as total')
            ->groupBy('mois')
            ->orderBy('mois')
            ->pluck('total', 'mois')
            ->toArray();

        $dataGraphique = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataGraphique[] = $ventesParMois[$i] ?? 0;
        }

        return view('dashboard', compact(
            'caTotal',
            'devisEnAttente',
            'topClients',
            'dataGraphique',
            'clients',
            'clientId'
        ));
    }
}
