<x-app-layout>
    <div class="max-w-2xl mx-auto py-12">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">

            <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center {{ $product->stock_actuel <= $product->seuil_alerte ? 'bg-red-50' : 'bg-green-50' }}">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $product->designation }}</h2>
                    <p class="text-sm text-gray-500">Réf: {{ $product->reference ?? 'N/A' }}</p>
                </div>
                <div class="text-right">
                    @if($product->stock_actuel <= $product->seuil_alerte)
                        <span class="bg-red-200 text-red-800 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">Stock Critique</span>
                    @else
                        <span class="bg-green-200 text-green-800 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">En Stock</span>
                    @endif
                </div>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-2 gap-8 mb-8">
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-bold mb-1">Prix de vente HT</p>
                        <p class="text-3xl font-bold text-gray-800">{{ number_format($product->prix_ht, 2, ',', ' ') }} €</p>
                        <p class="text-xs text-gray-500 mt-1">TVA: {{ $product->tva }}%</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400 uppercase font-bold mb-1">Quantité en stock</p>
                        <p class="text-3xl font-bold {{ $product->stock_actuel <= $product->seuil_alerte ? 'text-red-600' : 'text-green-600' }}">{{ $product->stock_actuel }}</p>
                        <p class="text-xs text-gray-500 mt-1">Seuil alerte: {{ $product->seuil_alerte }}</p>
                    </div>
                </div>

                <div class="mb-8">
                    <p class="text-xs text-gray-400 uppercase font-bold mb-2">Description</p>
                    <div class="bg-gray-50 p-4 rounded-lg text-gray-700 text-sm leading-relaxed border border-gray-200">
                        {{ $product->description ?: 'Aucune description disponible.' }}
                    </div>
                </div>

                <div class="flex justify-between items-center pt-6 border-t border-gray-100">
                    <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-800 font-medium flex items-center gap-1">
                        ← Retour
                    </a>
                    <div class="flex gap-3">
                        <form action="{{ route('products.destroy', $product->id_produit) }}" method="POST" onsubmit="return confirm('Supprimer ce produit ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition font-medium">
                                Supprimer
                            </button>
                        </form>
                        <a href="{{ route('products.edit', $product->id_produit) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition">
                            Modifier
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
