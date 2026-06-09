<x-app-layout>
    {{-- On passe les clients ET les produits au JS --}}
    <div x-data="documentEditor({{ json_encode($clients) }}, {{ json_encode($products) }})" class="flex flex-col lg:flex-row h-[calc(100vh-65px)]">

        <div class="w-full lg:w-5/12 h-full overflow-y-auto bg-white border-r border-gray-200 shadow-[4px_0_24px_rgba(0,0,0,0.02)] z-10">

            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" id="docForm" class="p-6">
                @csrf

                <input type="hidden" name="numero" value="{{ $prochainNumero }}">

                <div class="flex justify-between items-center mb-6 sticky top-0 bg-white z-20 pb-4 border-b border-gray-50 pt-2">
                    <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-600 p-1.5 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </span>
                        Éditeur {{ $typeParDefaut }}
                    </h1>
                    <div class="flex gap-2">
                        <a href="{{ route('documents.index') }}" class="px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 rounded-lg transition">Annuler</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded-lg shadow-md transition transform hover:scale-105 flex items-center gap-2">
                            <span>Sauvegarder</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 block">Design du document</label>
                    <div class="grid grid-cols-4 gap-2">
                        <div @click="template = 'classic'" :class="{'ring-2 ring-blue-500 bg-blue-50 border-blue-200': template === 'classic', 'border-gray-200 hover:border-gray-300': template !== 'classic'}" class="cursor-pointer border rounded-lg p-2 text-center transition group">
                            <div class="h-10 bg-white border border-gray-100 mb-1 rounded flex flex-col p-0.5 shadow-sm">
                                <div class="h-1.5 w-full bg-blue-600 mb-0.5 rounded-sm"></div>
                                <div class="h-0.5 w-1/2 bg-gray-200"></div>
                            </div>
                            <span class="text-[10px] font-bold text-gray-600 uppercase">Classic</span>
                        </div>
                        <div @click="template = 'minimal'" :class="{'ring-2 ring-blue-500 bg-blue-50 border-blue-200': template === 'minimal', 'border-gray-200 hover:border-gray-300': template !== 'minimal'}" class="cursor-pointer border rounded-lg p-2 text-center transition">
                            <div class="h-10 bg-white border border-gray-100 mb-1 rounded flex flex-col p-0.5 items-center justify-center shadow-sm">
                                <div class="h-0.5 w-1/2 bg-black mb-1"></div>
                                <div class="h-px w-3/4 bg-gray-200"></div>
                            </div>
                            <span class="text-[10px] font-bold text-gray-600 uppercase">Minimal</span>
                        </div>
                        <div @click="template = 'modern'" :class="{'ring-2 ring-blue-500 bg-blue-50 border-blue-200': template === 'modern', 'border-gray-200 hover:border-gray-300': template !== 'modern'}" class="cursor-pointer border rounded-lg p-2 text-center transition">
                            <div class="h-10 bg-white border border-gray-100 mb-1 rounded flex flex-row shadow-sm overflow-hidden">
                                <div class="w-1/3 bg-indigo-500 h-full"></div>
                                <div class="w-2/3 p-0.5"><div class="h-0.5 w-full bg-gray-200"></div></div>
                            </div>
                            <span class="text-[10px] font-bold text-gray-600 uppercase">Modern</span>
                        </div>
                        <div @click="template = 'bold'" :class="{'ring-2 ring-blue-500 bg-blue-50 border-blue-200': template === 'bold', 'border-gray-200 hover:border-gray-300': template !== 'bold'}" class="cursor-pointer border rounded-lg p-2 text-center transition">
                            <div class="h-10 bg-gray-900 border border-gray-800 mb-1 rounded flex flex-col p-0.5 shadow-sm">
                                <div class="h-2 w-2 bg-yellow-400 rounded-full mb-0.5"></div>
                            </div>
                            <span class="text-[10px] font-bold text-gray-600 uppercase">Bold</span>
                        </div>
                    </div>
                    <input type="hidden" name="design_template" x-model="template">
                </div>

                <div class="mb-8">
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 block">Logo</label>
                    <div
                        class="border-2 border-dashed rounded-lg p-4 flex flex-col items-center justify-center cursor-pointer transition-colors relative bg-gray-50"
                        :class="{'border-blue-500 bg-blue-50': dragover, 'border-gray-300 hover:border-gray-400': !dragover}"
                        @dragover.prevent="dragover = true"
                        @dragleave.prevent="dragover = false"
                        @drop.prevent="handleDrop($event)"
                        @click="$refs.fileInput.click()"
                    >
                        <template x-if="!logoPreview">
                            <div class="text-center py-2">
                                <svg class="mx-auto h-8 w-8 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="text-xs text-gray-500">Glisser logo ou <span class="text-blue-600 font-medium">parcourir</span></p>
                            </div>
                        </template>
                        <template x-if="logoPreview">
                            <div class="relative group w-full flex justify-center">
                                <img :src="logoPreview" class="h-16 object-contain">
                                <div class="absolute inset-0 bg-white/80 flex items-center justify-center opacity-0 group-hover:opacity-100 transition rounded backdrop-blur-sm">
                                    <span class="text-gray-800 text-xs font-bold">Changer</span>
                                </div>
                            </div>
                        </template>
                        <input type="file" name="logo" x-ref="fileInput" class="hidden" accept="image/*" @change="handleFileSelect($event)">
                    </div>
                </div>

                <hr class="border-gray-100 mb-6">

                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Client</label>
                        <select name="id_client" x-model="selectedClient" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">-- Sélectionner un client --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id_client }}">{{ $client->raison_sociale ?? $client->nom . ' ' . $client->prenom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Date d'émission</label>
                            <input type="date" name="date_emission" x-model="dateEmission" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Date d'échéance</label>
                            <input type="date" name="date_echeance" x-model="dateEcheance" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <input type="hidden" name="type_document" value="{{ $typeParDefaut }}">

                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-xs font-bold text-gray-500 uppercase">Lignes du devis</h3>
                            <button type="button" @click="addItem()" class="text-xs bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-2 py-1 rounded shadow-sm transition">
                                + Ajouter
                            </button>
                        </div>


                        <div class="space-y-3">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm relative group">

                                    <div class="mb-2">
                                        <select
                                            x-model="item.product_id"
                                            @change="fillProduct(index)"
                                            class="w-full text-xs border-gray-200 rounded p-1.5 bg-blue-50 text-blue-800 font-semibold focus:ring-blue-500 border-none"
                                        >
                                            <option value="">-- Choisir un produit (Optionnel) --</option>
                                            <template x-for="prod in products">
                                                <option :value="prod.id_produit" x-text="prod.designation + ' (' + parseFloat(prod.prix_ht).toFixed(2) + '€)'"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-12 gap-2">
                                        <div class="col-span-2">
                                            <label class="text-[10px] text-gray-400 block">Qté</label>
                                            <input x-model="item.quantite" type="number" step="0.1" class="w-full text-xs border-gray-200 rounded p-1 text-right bg-gray-50">

                                            <div x-show="isStockInsufficient(item)" class="text-red-500 text-[10px] mt-1 font-bold flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                <span>Stock bas (Reste: <span x-text="getStockAmount(item)"></span>)</span>
                                            </div>
                                        </div>
                                        <div class="col-span-3">
                                            <label class="text-[10px] text-gray-400 block">Prix</label>
                                            <input x-model="item.prix" type="number" step="0.01" class="w-full text-xs border-gray-200 rounded p-1 text-right bg-gray-50">
                                        </div>


                                        <div class="col-span-2">
                                            <label class="text-[10px] text-gray-400 block">Remise %</label>
                                            <input x-model="item.remise" type="number" step="0.01" class="w-full text-xs border-gray-200 rounded p-1 text-right bg-gray-50">
                                        </div>
                                        <div class="col-span-3">
                                            <label class="text-[10px] text-gray-400 block">TVA %</label>
                                            <select x-model="item.tva" class="w-full text-xs border-gray-200 rounded p-1 text-right bg-gray-50 pr-4">
                                                <option value="20">20</option>
                                                <option value="10">10</option>
                                                <option value="5.5">5.5</option>
                                                <option value="2.1">2.1</option>
                                                <option value="0">0</option>
                                            </select>
                                        </div>
                                        <div class="col-span-2 flex items-end justify-end">
                                            <button type="button" @click="removeItem(index)" class="text-red-400 hover:text-red-600 p-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <input type="hidden" :name="'lignes['+index+'][description]'" :value="item.description">
                                    <input type="hidden" :name="'lignes['+index+'][quantite]'" :value="item.quantite">
                                    <input type="hidden" :name="'lignes['+index+'][prix_unitaire]'" :value="item.prix">
                                    <input type="hidden" :name="'lignes['+index+'][tva]'" :value="item.tva">


                                    <input type="hidden" :name="'lignes['+index+'][remise_percent]'" :value="item.remise">
                                </div>
                            </template>
                        </div>
                    </div>


                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Remise globale sur le devis (%)</label>
                        <input type="number" name="remise_globale" x-model="remiseGlobale" step="0.01" min="0" max="100" class="w-1/3 rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ex: 5">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Conditions & Notes</label>
                        <textarea name="notes" x-model="notes" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" rows="3" placeholder="Paiement à 30 jours..."></textarea>
                    </div>
                </div>
            </form>
        </div>

        <div class="w-full lg:w-7/12 h-full bg-slate-800 overflow-y-auto p-3 flex justify-center items-start shadow-inner">
            <div
                class="bg-white shadow-2xl transition-all p-2 duration-500 ease-in-out relative origin-top transform scale-[0.6] sm:scale-[0.7] md:scale-[0.8] lg:scale-[0.65] xl:scale-[0.85] 2xl:scale-100"
                style="width: 210mm; min-height: 297mm;"
                :class="{
                    'p-16 font-serif text-gray-800': template === 'classic',
                    'p-16 font-sans text-gray-900 border-t-[20px] border-black': template === 'minimal',
                    'font-sans bg-gray-50': template === 'bold',
                    'font-sans flex flex-row': template === 'modern',
                }"
            >
                <template x-if="template === 'modern'">
                    <div class="w-1/3 bg-indigo-900 text-white p-10 min-h-[297mm] flex flex-col">
                        <img :src="logoPreview || 'https://via.placeholder.com/150/e0e7ff/4f46e5?text=LOGO'" class="w-32 mb-10 bg-white p-2 rounded-lg shadow-lg">
                        <div class="mb-8">
                            <h2 class="font-bold text-xs opacity-50 uppercase tracking-widest mb-1">Émetteur</h2>
                            <p class="font-bold text-lg leading-tight">{{ Auth::user()->name }}</p>
                            <p class="text-indigo-200 text-sm mt-1">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="mb-auto">
                            <h2 class="font-bold text-xs opacity-50 uppercase tracking-widest mb-1">Destinataire</h2>
                            <p class="font-bold text-lg leading-tight text-white" x-text="getClientName()"></p>
                            <p class="text-indigo-200 text-sm mt-1" x-text="getClientAddress()"></p>
                            <p class="text-indigo-200 text-sm" x-text="getClientCity()"></p>
                        </div>
                        <div class="border-t border-indigo-700 pt-6">
                            <p class="text-xs text-indigo-300">Merci de votre confiance.</p>
                        </div>
                    </div>
                </template>

                <div :class="{'w-full': template !== 'modern', 'w-2/3 p-10 bg-white flex flex-col justify-between': template === 'modern'}">
                    <div class="flex justify-between items-start mb-16" x-show="template !== 'modern'">
                        <div>
                            <template x-if="logoPreview">
                                <img :src="logoPreview" class="h-24 object-contain mb-4">
                            </template>
                            <template x-if="!logoPreview">
                                <div class="h-20 w-20 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 mb-4 border-2 border-dashed border-gray-300">
                                    <span class="text-[10px] font-bold">LOGO</span>
                                </div>
                            </template>
                            <div :class="{'text-gray-500': template === 'classic'}">
                                <p class="font-bold">{{ Auth::user()->name }}</p>
                                <p class="text-sm">12 Avenue du Code</p>
                                <p class="text-sm">69000 Lyon, France</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <h2 class="text-5xl font-bold tracking-tight mb-2 opacity-90"
                                :class="{'text-blue-600': template === 'classic', 'text-black': template === 'minimal', 'text-yellow-500': template === 'bold'}">
                                {{ strtoupper($typeParDefaut) }}
                            </h2>
                            <p class="text-gray-500 font-medium text-lg">{{ $prochainNumero }}</p>
                            <p class="text-gray-400 text-sm mt-1" x-text="'Date : ' + formatDate(dateEmission)"></p>
                            <p class="text-gray-400 text-sm mt-1" x-text="'Fin de validité : ' + formatDate(dateEcheance)"></p>
                        </div>
                    </div>

                    <div x-show="template === 'modern'" class="mb-10 text-right">
                        <h2 class="text-4xl font-bold text-indigo-900 mb-2">{{ strtoupper($typeParDefaut) }}</h2>
                        <p class="text-indigo-400 font-medium">{{ $prochainNumero }}</p>
                        <p class="text-gray-400 text-sm mt-1" x-text="formatDate(dateEmission)"></p>
                        <p class="text-gray-400 text-sm mt-1" x-text="formatDate(dateEcheance)"></p>
                    </div>

                    <div class="mb-16 pl-8 border-l-4"
                         :class="{'border-blue-600': template === 'classic', 'border-black': template === 'minimal', 'border-yellow-400': template === 'bold'}"
                         x-show="template !== 'modern'">
                        <h3 class="text-xs font-bold uppercase text-gray-400 mb-2 tracking-wider">Facturé à</h3>
                        <p class="font-bold text-2xl text-gray-900" x-text="getClientName()"></p>
                        <p class="text-gray-500" x-text="getClientAddress()"></p>
                        <p class="text-gray-500" x-text="getClientCity()"></p>
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
                                <th class="py-3 px-4 text-right text-xs uppercase font-bold tracking-wider rounded-tr-lg">Total HT</th>
                            </tr>
                            </thead>
                            <tbody :class="{'bg-white': template === 'bold', 'text-gray-600': true}">
                            <template x-for="item in items">
                                <tr class="border-b border-gray-100 last:border-0">
                                    <td class="py-4 px-4 text-sm font-medium text-gray-900"
                                        x-text="products.find(p => p.id_produit == item.product_id)?.designation || item.description || '...'">
                                    </td>
                                    <td class="py-4 px-4 text-sm text-right" x-text="item.quantite"></td>
                                    <td class="py-4 px-4 text-sm text-right" x-text="parseFloat(item.prix).toFixed(2) + ' €'"></td>
                                    <td class="py-4 px-4 text-sm text-right font-bold text-gray-900" x-text="((item.quantite * item.prix) - ((item.quantite * item.prix) * (Number(item.remise) / 100))).toFixed(2) + ' €'"></td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end mb-12">
                        <div class="w-1/2 space-y-3">
                            <div class="flex justify-between text-sm text-gray-500">
                                <span>Total HT</span>
                                <span class="font-medium" x-text="calculateTotalHT() + ' €'"></span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-500">
                                <span>TVA (Moyenne)</span>
                                <span class="font-medium" x-text="calculateTotalTVA() + ' €'"></span>
                            </div>

                            <template x-if="remiseGlobale > 0">
                                <div class="flex justify-between text-sm text-red-500 font-medium">
                                    <span>Remise globale (<span x-text="remiseGlobale"></span>%)</span>
                                    <span x-text="'-' + (((Number(calculateTotalHT()) + Number(calculateTotalTVA())) * (Number(remiseGlobale) / 100)).toFixed(2)) + ' €'"></span>
                                </div>
                            </template>

                            <div class="border-t border-gray-200 pt-3 flex justify-between items-center">
                                <span class="text-lg font-bold">Total TTC</span>
                                <span class="text-2xl font-bold"
                                      :class="{'text-blue-600': template === 'classic', 'text-black': template === 'minimal', 'text-yellow-600': template === 'bold', 'text-indigo-600': template === 'modern'}">
                                    <span x-text="calculateTotalTTC()"></span> €
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto pt-8 border-t border-gray-100 text-xs text-gray-400 text-center">
                        <p class="font-medium text-gray-600 mb-1">Conditions de règlement</p>
                        <p x-text="notes || 'Paiement à réception de facture. Aucun escompte pour paiement anticipé.'"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function documentEditor(clientRecus, productsRecus){
            return{
                clients : clientRecus,
                products : productsRecus, // Liste des produits reçue du contrôleur
                template: 'classic',
                selectedClient:'',
                dateEmission:'',
                dateEcheance:'',
                notes:'',
                remiseGlobale: 0,
                items: [
                    { product_id: '', description: 'Service initial', quantite: 1, prix: 0, tva: 20, remise: 20 }
                ],
                logoPreview: null,
                dragover: false,

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (file) this.readFile(file);
                },

                handleDrop(event) {
                    this.dragover = false;
                    const file = event.dataTransfer.files[0];
                    if (file) {
                        this.readFile(file);
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        this.$refs.fileInput.files = dataTransfer.files;
                    }
                },

                readFile(file) {
                    if (!file.type.match('image.*')) return alert('Seules les images sont autorisées');
                    const reader = new FileReader();
                    reader.onload = (e) => { this.logoPreview = e.target.result; };
                    reader.readAsDataURL(file);
                },

                // Ajout d'une ligne vide
                addItem(){
                    this.items.push({
                        product_id: '',
                        description: '',
                        quantite: 1,
                        prix: 0,
                        tva: 20,
                        remise: 0,
                    });
                },


                fillProduct(index) {
                    let item = this.items[index];
                    let selectedProduct = this.products.find(p => p.id_produit == item.product_id);

                    if (selectedProduct) {

                        let fullDesc = selectedProduct.designation;
                        if(selectedProduct.description) {
                            fullDesc += ' - ' + selectedProduct.description;
                        }

                        item.description = fullDesc;
                        item.prix = selectedProduct.prix_ht;
                        item.tva = selectedProduct.tva || 20;
                    }
                },

                isStockInsufficient(item) {
                    if (!item.product_id) return false;
                    const product = this.products.find(p => p.id_produit == item.product_id);
                    if (!product) return false;
                    // Vérifie si la quantité demandée est supérieure au stock
                    return parseFloat(item.quantite) > parseFloat(product.stock_actuel);
                },

                getStockAmount(item) {
                    const product = this.products.find(p => p.id_produit == item.product_id);
                    return product ? product.stock_actuel : 0;
                },


                removeItem(index){
                    if(this.items.length > 1) {
                        this.items.splice(index, 1)
                    }
                },

                calculateTotalHT(){
                    let total =0;
                    this.items.forEach(item => {
                        let montantLigne = Number(item.quantite) * Number(item.prix);

                        montantLigne = montantLigne - (montantLigne * (Number(item.remise) / 100));

                        total += montantLigne;
                    })
                    return total.toFixed(2);
                },

                calculateTotalTVA(){
                    const totalTVA = this.items.reduce((sum, item)=> {
                        let ligneHT = Number(item.quantite) * Number(item.prix);

                        ligneHT = ligneHT - (ligneHT * (Number(item.remise) / 100));

                        return sum + (ligneHT *(Number(item.tva)/100));
                    },0);
                    return totalTVA.toFixed(2);
                },

                calculateTotalTTC(){

                    let totalTTC = Number(this.calculateTotalHT()) + Number(this.calculateTotalTVA());

                    totalTTC = totalTTC - (totalTTC * (Number(this.remiseGlobale) / 100));

                    return totalTTC.toFixed(2);
                },

                getClientName(){
                    const client = this.clients.find(c => c.id_client == this.selectedClient);
                    if(client){
                        return client.raison_sociale || (client.nom + ' ' + client.prenom)
                    }
                    return "choisir un client"

                },

                getClientAddress(){
                    const client = this.clients.find(c => c.id_client == this.selectedClient);
                    if (client && client.adresse) {
                        return client.adresse;
                    }
                    return 'Adresse du client...';

                },

                getClientCity(){
                    const client = this.clients.find(c => c.id_client == this.selectedClient);
                    if (client) {
                        const cp = client.code_postal || '';
                        const ville = client.ville || '';
                        if (!cp && !ville) return 'Code Postal, Ville';

                        return cp + ' ' + ville;
                    }
                    return 'Code Postal, Ville';
                },

                formatDate(dateStr) {
                    if (!dateStr) return '';
                    const date = new Date(dateStr);
                    return date.toLocaleDateString('fr-FR');
                }
            }
        }
    </script>
</x-app-layout>
