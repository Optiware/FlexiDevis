<x-app-layout>
    <div class="max-w-3xl mx-auto py-12">
        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">

            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Nouveau Produit</h2>
                <a href="{{ route('products.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Retour au stock</a>
            </div>

            <form action="{{ route('products.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Référence (Optionnel)</label>
                        <input type="text" name="reference" placeholder="Ex: REF-001" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Désignation Produit <span class="text-red-500">*</span></label>
                        <input type="text" name="designation" placeholder="Ex: Disque Dur SSD 1To" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description / Détails</label>
                    <textarea name="description" rows="3" placeholder="Caractéristiques techniques, garantie, marque..." class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Prix Unitaire HT (€) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="prix_ht" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">TVA (%)</label>
                        <select name="tva" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 bg-white">
                            <option value="20.00">20% (Standard)</option>
                            <option value="10.00">10% (Intermédiaire)</option>
                            <option value="5.50">5.5% (Réduit)</option>
                            <option value="2.10">2.1% (Super réduit)</option>
                            <option value="0.00">0% (Exonéré)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Stock Initial <span class="text-red-500">*</span></label>
                        <input type="number" name="stock_actuel" required value="0" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Quantité actuellement disponible.</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Seuil d'alerte</label>
                        <input type="number" name="seuil_alerte" value="5" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Déclenche l'alerte rouge si le stock descend en dessous.</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                    <a href="{{ route('products.index') }}" class="px-6 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">Annuler</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-8 rounded-lg shadow-md transition transform hover:scale-105">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
