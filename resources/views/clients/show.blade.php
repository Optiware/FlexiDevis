<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Fiche Client
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-50 transition">
                    ← Retour
                </a>
                <a href="{{ route('clients.edit', $client->id_client) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-blue-700 transition">
                    Modifier
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-t-4 border-blue-500">
                        <div class="text-center mb-6">
                            <div class="h-20 w-20 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-2xl font-bold mx-auto mb-3 uppercase">
                                {{ substr($client->raison_sociale ?? $client->nom, 0, 2) }}
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">
                                {{ $client->raison_sociale ?? $client->nom . ' ' . $client->prenom }}
                            </h3>
                            @if($client->raison_sociale && $client->nom)
                                <p class="text-sm text-gray-500">Contact : {{ $client->nom }} {{ $client->prenom }}</p>
                            @endif
                        </div>

                        <div class="space-y-4 border-t border-gray-100 pt-4">
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase">Email</label>
                                <p class="text-sm text-gray-800 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    <a href="mailto:{{ $client->email }}" class="hover:underline hover:text-blue-600">{{ $client->email }}</a>
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase">Téléphone</label>
                                <p class="text-sm text-gray-800 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2 3 3 0 003 3 2 2 0 012 2 3 3 0 003 3 2 2 0 012 2v3a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    {{ $client->telephone ?? 'Non renseigné' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase">Adresse</label>
                                <p class="text-sm text-gray-800 flex items-start gap-2">
                                    <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span>
                                        {{ $client->adresse }}<br>
                                        {{ $client->code_postal }} {{ $client->ville }}
                                    </span>
                                </p>
                            </div>
                            @if($client->siret)
                                <div>
                                    <label class="text-xs font-bold text-gray-400 uppercase">SIRET</label>
                                    <p class="text-sm text-gray-800">{{ $client->siret }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <form action="{{ route('clients.destroy', $client->id_client) }}" method="POST" onsubmit="return confirm('Attention ! Supprimer ce client supprimera aussi tous ses documents. Continuer ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full text-center text-red-600 hover:text-red-800 text-sm font-medium hover:underline">
                                    Supprimer ce client
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-6">

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-xs font-bold uppercase">Total Facturé (TTC)</p>
                                <p class="text-2xl font-bold text-gray-800">{{ number_format($totalAchat, 2, ',', ' ') }} €</p>
                            </div>
                            <div class="p-3 bg-green-50 text-green-600 rounded-full">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-xs font-bold uppercase">Documents</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $client->documents->count() }}</p>
                            </div>
                            <div class="p-3 bg-blue-50 text-blue-600 rounded-full">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800">Historique des documents</h3>
                            <a href="{{ route('documents.create', ['type' => 'Devis']) }}?client_id={{$client->id_client}}" class="text-xs bg-blue-50 text-blue-600 px-3 py-1 rounded-full hover:bg-blue-100 transition font-medium">
                                + Créer un document
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N°</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Montant TTC</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Statut</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($client->documents as $doc)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($doc->date_emission)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-800">
                                            {{ $doc->numero }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $doc->type_document }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                                            {{ number_format($doc->total_ttc, 2, ',', ' ') }} €
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @php
                                                $statusColors = [
                                                    'Brouillon' => 'bg-gray-100 text-gray-600',
                                                    'Valide'    => 'bg-blue-100 text-blue-700',
                                                    'Accepte'   => 'bg-green-100 text-green-700',
                                                    'Paye'      => 'bg-green-100 text-green-800',
                                                    'Impaye'    => 'bg-red-50 text-red-600',
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$doc->statut] ?? 'bg-gray-100' }}">
                                                    {{ $doc->statut }}
                                                </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('documents.show', $doc->id_document) }}" class="text-blue-600 hover:underline">Voir</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-sm">
                                            Aucun document pour ce client.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
