<x-app-layout>
    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-[210mm] mx-auto transition-transform origin-top print:transform-none print:m-0 print:w-full">

            <div class="flex justify-between items-center mb-6 px-4 sm:px-0 no-print">
                <h1 class="text-xl font-bold text-gray-800">
                    {{ $document->type_document }} N° {{ $document->numero }}
                </h1>

                <div class="flex space-x-3">
                    <a href="{{ route('documents.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                        Retour
                    </a>
                    <a href="{{ route('documents.edit', $document->id_document) }}" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                        Modifier
                    </a>
                    <button onclick="window.print()" class="px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Imprimer
                    </button>
                </div>
            </div>

        </div>

        <div class="py-12" id="print-area">
            <div x-data="{ template: '{{ $document->design_template ?? 'classic' }}' }" class="max-w-4xl mx-auto sm:px-6 lg:px-8 print:max-w-full print:px-0">

                <div
                    class="bg-white shadow-lg sm:rounded-lg print:shadow-none"
                    :class="{
                        'p-16 font-serif text-gray-800': template === 'classic',
                        'p-16 font-sans text-gray-900 border-t-[20px] border-black': template === 'minimal',
                        'font-sans bg-gray-50': template === 'bold',
                        'font-sans flex flex-row': template === 'modern',
                    }"
                    style="min-height: 297mm;"
                >

                    <template x-if="template === 'modern'">
                        <div class="w-1/3 bg-indigo-900 text-white p-10 min-h-[297mm] flex flex-col print:h-full">
                            @if($document->logo_path)
                                <img src="{{ asset('storage/' . $document->logo_path) }}" class="w-32 mb-10 bg-white p-2 rounded-lg shadow-lg object-contain">
                            @else
                                <div class="w-32 h-32 mb-10 bg-white/10 rounded flex items-center justify-center text-xs">Logo</div>
                            @endif

                            <div class="mb-8">
                                <h2 class="font-bold text-xs opacity-50 uppercase tracking-widest mb-1">Émetteur</h2>
                                <p class="font-bold text-lg leading-tight">{{ auth()->user()->raison_sociale ?? auth()->user()->name }}</p>
                                <p class="text-indigo-200 text-sm mt-1">{{ auth()->user()->email }}</p>
                                <p class="text-indigo-200 text-sm">{{ auth()->user()->telephone }}</p>
                                <p class="text-indigo-200 text-sm mt-2">{{ auth()->user()->adresse }}</p>
                                <p class="text-indigo-200 text-sm">{{ auth()->user()->code_postal }} {{ auth()->user()->ville }}</p>
                            </div>

                            <div class="mb-auto">
                                <h2 class="font-bold text-xs opacity-50 uppercase tracking-widest mb-1">Destinataire</h2>
                                <p class="font-bold text-lg leading-tight text-white">{{ $document->client->raison_sociale ?? $document->client->nom . ' ' . $document->client->prenom }}</p>
                                <p class="text-indigo-200 text-sm mt-1">{{ $document->client->adresse }}</p>
                                <p class="text-indigo-200 text-sm">{{ $document->client->code_postal }} {{ $document->client->ville }}</p>
                            </div>

                            <div class="border-t border-indigo-700 pt-6">
                                <p class="text-xs text-indigo-300">Merci de votre confiance.</p>
                            </div>
                        </div>
                    </template>

                    <div :class="{'w-full': template !== 'modern', 'w-2/3 p-10 bg-white flex flex-col justify-between': template === 'modern'}">

                        <div class="flex justify-between items-start mb-16" x-show="template !== 'modern'">
                            <div>
                                @if($document->logo_path)
                                    <img src="{{ asset('storage/' . $document->logo_path) }}" class="h-24 object-contain mb-4">
                                @else
                                    <div class="h-20 w-20 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 mb-4 border-2 border-dashed border-gray-300">
                                        <span class="text-[10px] font-bold">{{ auth()->user()->raison_sociale ?? 'LOGO' }}</span>
                                    </div>
                                @endif

                                <div :class="{'text-gray-500': template === 'classic'}">
                                    <p class="font-bold">{{ auth()->user()->raison_sociale ?? auth()->user()->name }}</p>
                                    <p class="text-sm">{{ auth()->user()->adresse }}</p>
                                    <p class="text-sm">{{ auth()->user()->code_postal }} {{ auth()->user()->ville }}</p>
                                    <p class="text-sm">{{ auth()->user()->email }}</p>
                                    <p class="text-sm">{{ auth()->user()->telephone }}</p>
                                    <p class="text-sm">SIRET: {{ auth()->user()->siret }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <h2 class="text-5xl font-bold tracking-tight mb-2 opacity-90"
                                    :class="{'text-blue-600': template === 'classic', 'text-black': template === 'minimal', 'text-yellow-500': template === 'bold'}">
                                    {{ strtoupper($document->type_document) }}
                                </h2>
                                <p class="text-gray-500 font-medium text-lg">{{ $document->numero }}</p>
                                <p class="text-gray-400 text-sm mt-1">Date : {{ \Carbon\Carbon::parse($document->date_emission)->format('d/m/Y') }}</p>
                                @if($document->date_echeance)
                                    <p class="text-gray-400 text-sm mt-1">Échéance : {{ \Carbon\Carbon::parse($document->date_echeance)->format('d/m/Y') }}</p>
                                @endif
                            </div>
                        </div>

                        <div x-show="template === 'modern'" class="mb-10 text-right">
                            <h2 class="text-4xl font-bold text-indigo-900 mb-2">{{ strtoupper($document->type_document) }}</h2>
                            <p class="text-indigo-400 font-medium">{{ $document->numero }}</p>
                            <p class="text-gray-400 text-sm mt-1">Date : {{ \Carbon\Carbon::parse($document->date_emission)->format('d/m/Y') }}</p>
                        </div>

                        <div class="mb-16 pl-8 border-l-4"
                             :class="{'border-blue-600': template === 'classic', 'border-black': template === 'minimal', 'border-yellow-400': template === 'bold'}"
                             x-show="template !== 'modern'">
                            <h3 class="text-xs font-bold uppercase text-gray-400 mb-2 tracking-wider">Facturé à</h3>
                            <p class="font-bold text-2xl text-gray-900">{{ $document->client->raison_sociale ?? $document->client->nom . ' ' . $document->client->prenom }}</p>
                            <p class="text-gray-500">{{ $document->client->adresse }}</p>
                            <p class="text-gray-500">{{ $document->client->code_postal }} {{ $document->client->ville }}</p>
                        </div>

                        <div class="flex-grow">
                            <table class="w-full mb-12">
                                <thead>
                                <tr :class="{
                                        'border-b-2 border-blue-600 text-blue-600': template === 'classic',
                                        'bg-black text-white': template === 'minimal',
                                        'bg-gray-900 text-yellow-400 rounded-t-lg': template === 'bold',
                                        'bg-indigo-50 text-indigo-900 rounded-lg': template === 'modern'
                                    }">
                                    <th class="py-3 px-4 text-left text-xs uppercase font-bold tracking-wider rounded-tl-lg">Description</th>
                                    <th class="py-3 px-4 text-right text-xs uppercase font-bold tracking-wider">Qté</th>
                                    <th class="py-3 px-4 text-right text-xs uppercase font-bold tracking-wider">Prix Unitaire</th>
                                    <th class="py-3 px-4 text-right text-xs uppercase font-bold tracking-wider">Remise</th>
                                    <th class="py-3 px-4 text-right text-xs uppercase font-bold tracking-wider">TVA</th>
                                    <th class="py-3 px-4 text-right text-xs uppercase font-bold tracking-wider rounded-tr-lg">Total HT</th>
                                </tr>
                                </thead>
                                <tbody :class="{'bg-white': template === 'bold', 'text-gray-600': true}">
                                @foreach($document->lignes as $ligne)
                                    <tr class="border-b border-gray-100 last:border-0">
                                        <td class="py-4 px-4 text-sm font-medium text-gray-900">{{ $ligne->description }}</td>
                                        <td class="py-4 px-4 text-sm text-right">{{ $ligne->quantite }}</td>
                                        <td class="py-4 px-4 text-sm text-right">{{ number_format($ligne->prix_unitaire, 2, ',', ' ') }} €</td>
                                        <td class="py-4 px-4 text-sm text-right">
                                            @if($ligne->remise_percent > 0)
                                                -{{ $ligne->remise_percent }}%
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="py-4 px-4 text-sm text-right">{{ $ligne->taux_tva }}%</td>
                                        <td class="py-4 px-4 text-sm text-right font-bold text-gray-900">{{ number_format($ligne->montant_ht, 2, ',', ' ') }} €</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-end mb-12">
                            <div class="w-1/2 space-y-3">
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span>Total HT</span>
                                    <span class="font-medium">{{ number_format($document->total_ht, 2, ',', ' ') }} €</span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span>Total TVA</span>
                                    <span class="font-medium">{{ number_format($document->total_tva, 2, ',', ' ') }} €</span>
                                </div>

                                @if($document->remise_globale > 0)
                                    @php
                                        $totalAvantRemise = $document->total_ht + $document->total_tva;
                                        $montantRemise = $totalAvantRemise * ($document->remise_globale / 100);
                                    @endphp
                                    <div class="flex justify-between text-sm text-red-500 font-medium">
                                        <span>Remise globale ({{ $document->remise_globale }}%)</span>
                                        <span>- {{ number_format($montantRemise, 2, ',', ' ') }} €</span>
                                    </div>
                                @endif

                                <div class="border-t border-gray-200 pt-3 flex justify-between items-center">
                                    <span class="text-lg font-bold">Total TTC</span>
                                    <span class="text-2xl font-bold"
                                          :class="{'text-blue-600': template === 'classic', 'text-black': template === 'minimal', 'text-yellow-600': template === 'bold', 'text-indigo-600': template === 'modern'}">
                                        {{ number_format($document->total_ttc, 2, ',', ' ') }} €
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto pt-8 border-t border-gray-100 text-xs text-gray-400 text-center">
                            <p class="font-medium text-gray-600 mb-1">Conditions de règlement</p>
                            <p>{{ $document->notes ?? 'Paiement à réception de facture.' }}</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            @page {
                size: A4;
                margin: 0;
            }

            html, body {
                margin: 0;
                padding: 0;
                width: 210mm;
                height: 297mm;
                overflow: hidden;
                background-color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            #print-area {
                visibility: visible;
                position: absolute;
                left: 0;
                top: 0;
                width: 210mm;
                height: 297mm;
                max-height: 297mm;
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden;
            }

            .no-print {
                display: none !important;
            }

            #print-area * {
                visibility: visible;
            }
        }
    </style>
</x-app-layout>
