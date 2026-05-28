<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex flex-col md:flex-row justify-between items-end mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Tableau de Bord</h1>
                    <p class="text-gray-500">
                        @if($clientId)
                            Vue filtrée pour le client sélectionné.
                        @else
                            Vue globale de votre activité.
                        @endif
                    </p>
                </div>

                <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2 bg-white p-2 rounded-lg shadow-sm border border-gray-200">
                    <svg class="w-5 h-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    <select name="client_id" onchange="this.form.submit()" class="border-none text-sm focus:ring-0 text-gray-700 bg-transparent cursor-pointer min-w-[200px]">
                        <option value=""> Vue Globale </option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id_client }}" {{ $clientId == $c->id_client ? 'selected' : '' }}>
                                {{ $c->raison_sociale ?? $c->nom . ' ' . $c->prenom }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <a href="{{ route('documents.create', ['type' => 'Devis']) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm flex items-center gap-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Créer un Devis
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-green-500">
                    <div class="flex justify-between items-start mb-2">
                        <div class="p-2 bg-green-50 rounded-lg text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <p class="text-gray-500 text-xs font-bold uppercase">Chiffre d'Affaires HT</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ number_format($caTotal, 2, ',', ' ') }} €</h3>
                    <p class="text-xs text-gray-400 mt-1">Factures validées ou payées</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-blue-500">
                    <div class="flex justify-between items-start mb-2">
                        <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                    </div>
                    <p class="text-gray-500 text-xs font-bold uppercase">Devis en brouillon</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $devisEnAttente }}</h3>
                    <p class="text-xs text-gray-400 mt-1">Opportunités à signer</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-purple-500">
                    <div class="flex justify-between items-start mb-2">
                        <div class="p-2 bg-purple-50 rounded-lg text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 005.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>
                    <p class="text-gray-500 text-xs font-bold uppercase">
                        {{ $clientId ? 'Ce Client' : 'Meilleur Client' }}
                    </p>
                    @if($topClients->isNotEmpty())
                        <h3 class="text-xl font-bold text-gray-900 truncate" title="{{ $topClients->first()->raison_sociale ?? $topClients->first()->nom }}">
                            {{ Str::limit($topClients->first()->raison_sociale ?? $topClients->first()->nom, 20) }}
                        </h3>
                        <p class="text-xs text-purple-600 font-bold mt-1">
                            {{ number_format($topClients->first()->documents_sum_total_ht, 0, ',', ' ') }} € cumulés
                        </p>
                    @else
                        <h3 class="text-xl font-bold text-gray-400">--</h3>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg p-6 lg:col-span-2 border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        Évolution CA Facturé ({{ date('Y') }})
                    </h3>
                    <div class="relative h-64 w-full">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        {{ $clientId ? 'Détail Client' : '🏆 Top Clients' }}
                    </h3>
                    @if($topClients->isEmpty())
                        <p class="text-gray-500 text-sm italic">Aucune facture validée pour le moment.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($topClients as $index => $client)
                                <div class="flex items-center justify-between border-b border-gray-50 pb-2 last:border-0">
                                    <div class="flex items-center gap-3">
                                        @if(!$clientId)
                                            <div class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs
                                                {{ $index === 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $index + 1 }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">{{ $client->raison_sociale ?? $client->nom }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-gray-600">{{ number_format($client->documents_sum_total_ht, 0, ',', ' ') }} €</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900">
                        {{ $clientId ? 'Documents Récents du Client' : 'Derniers Documents' }}
                    </h3>
                    <a href="{{ route('documents.index') }}" class="text-sm text-blue-600 hover:underline">Voir tout</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                        <tr class="text-gray-400 text-xs uppercase bg-gray-50">
                            <th class="px-6 py-4 font-semibold">Date</th>
                            <th class="px-6 py-4 font-semibold">ID</th>
                            <th class="px-6 py-4 font-semibold">Client</th>
                            <th class="px-6 py-4 font-semibold">Type</th>
                            <th class="px-6 py-4 font-semibold">Montant TTC</th>
                            <th class="px-6 py-4 font-semibold">Statut</th>
                            <th class="px-6 py-4 font-semibold text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                        @php
                            // On refait une petite requête locale pour afficher les docs récents
                            // En production, on passerait ça via le contrôleur, mais pour simplifier ici on utilise le modèle direct ou le client filtré
                            if($clientId && $topClients->isNotEmpty()) {
                                // Si on filtre, on prend les docs du client (attention, $topClients est une collection de Clients)
                                // Le plus propre est d'utiliser auth()->user()->documents()... avec filtre
                                $docsRecents = \App\Models\Document::where('id_utilisateur', auth()->id())
                                                ->where('id_client', $clientId)
                                                ->orderBy('created_at', 'desc')
                                                ->take(5)->get();
                            } else {
                                $docsRecents = auth()->user()->documents()->orderBy('created_at', 'desc')->take(5)->get();
                            }
                        @endphp

                        @forelse($docsRecents as $doc)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-gray-500">{{ \Carbon\Carbon::parse($doc->date_emission)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 font-medium text-blue-600">#{{ $doc->numero }}</td>
                                <td class="px-6 py-4 text-gray-900">{{ $doc->client->raison_sociale ?? $doc->client->nom }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $doc->type_document }}</td>
                                <td class="px-6 py-4 font-bold text-gray-900">{{ number_format($doc->total_ttc, 2, ',', ' ') }} €</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold
                                        {{ $doc->statut == 'Brouillon' ? 'bg-gray-100 text-gray-600' : '' }}
                                        {{ $doc->statut == 'Valide' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $doc->statut == 'Accepte' ? 'bg-green-100 text-green-700' : '' }}
                                        {{ $doc->statut == 'Paye' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                        {{ ($doc->statut == 'Refuse' || $doc->statut == 'Impaye') ? 'bg-red-100 text-red-700' : '' }}">
                                        {{ $doc->statut }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('documents.show', $doc->id_document) }}" class="text-gray-400 hover:text-blue-600 font-bold text-lg">...</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-400">Aucun document récent.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart');
        if(ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                    datasets: [{
                        label: 'Chiffre d\'Affaires (€)',
                        data: {{ json_encode($dataGraphique) }},
                        borderWidth: 3,
                        borderColor: '#4f46e5', // Couleur Indigo
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                        x: { grid: { display: false } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }
    </script>
</x-app-layout>
