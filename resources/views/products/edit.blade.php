<x-app-layout>
    <div class="max-w-3xl mx-auto py-12">
        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Modifier Produit</h2>
                <a href="{{ route('products.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Retour au stock</a>
            </div>

            <form action="{{ route('products.update', $product->id_produit) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Référence (Optionnel)</label>
                        <input type="text" name="reference" value="{{ old('reference', $product->reference) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Désignation Produit <span class="text-red-500">*</span></label>
                        <input type="text" name="designation" value="{{ old('designation', $product->designation) }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description / Détails</label>
                    <textarea name="description" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Prix Unitaire HT (€) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="prix_ht" value="{{ old('prix_ht', $product->prix_ht) }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">TVA (%)</label>
                        <select name="tva" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white">
                            <option value="20.00" {{ $product->tva == 20.00 ? 'selected' : '' }}>20% (Standard)</option>
                            <option value="10.00" {{ $product->tva == 10.00 ? 'selected' : '' }}>10% (Intermédiaire)</option>
                            <option value="5.50" {{ $product->tva == 5.50 ? 'selected' : '' }}>5.5% (Réduit)</option>
                            <option value="2.10" {{ $product->tva == 2.10 ? 'selected' : '' }}>2.1% (Super réduit)</option>
                            <option value="0.00" {{ $product->tva == 0.00 ? 'selected' : '' }}>0% (Exonéré)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Stock Actuel <span class="text-red-500">*</span></label>
                        <input type="number" name="stock_actuel" value="{{ old('stock_actuel', $product->stock_actuel) }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Seuil d'alerte</label>
                        <input type="number" name="seuil_alerte" value="{{ old('seuil_alerte', $product->seuil_alerte) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                    <a href="{{ route('products.index') }}" class="px-6 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">Annuler</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded-lg shadow-md transition transform hover:scale-105">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
