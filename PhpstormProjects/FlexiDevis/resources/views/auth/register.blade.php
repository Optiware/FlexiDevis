<x-guest-layout maxWidth="sm:max-w-5xl">
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-extrabold text-gray-900">Bienvenue sur FlexiDevis</h1>
        <p class="text-gray-600">Configurons votre espace de travail en quelques étapes.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="px-8 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-lg font-semibold mb-6 border-b pb-2">Information Légale</h2>

                <div class="mt-4">
                    <x-input-label for="name" value="Nom de la société / Nom" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="email" value="Email Professionnel" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="siret" value="Numéro SIRET" />
                    <x-text-input id="siret" class="block mt-1 w-full" type="text" name="siret" :value="old('siret')" placeholder="123 456 789 00012" />
                    <x-input-error :messages="$errors->get('siret')" class="mt-2" />
                </div>

                <div class="mt-4" x-data="{ show: false }">
                    <x-input-label for="password" value="Mot de passe" />

                    <div class="relative">
                        <x-text-input
                            id="password"
                            class="block mt-1 w-full pr-10"
                            ::type="show ? 'text' : 'password'"
                            name="password"
                            required
                            autocomplete="new-password"
                        />

                        <button type="button"
                                @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 focus:outline-none">

                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>

                            <svg x-show="show" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />

                    @if ($errors->has('password'))
                        <p class="mt-1 text-xs text-gray-600 italic">
                            Assurez-vous d'avoir utilisé des majuscules et des chiffres si nécessaire.
                        </p>
                    @endif
                </div>

                <div class="mt-4" x-data="{ show: false }">
                    <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />

                    <div class="relative">
                        <x-text-input
                            id="password_confirmation"
                            class="block mt-1 w-full pr-10"
                            ::type="show ? 'text' : 'password'"
                            name="password_confirmation"
                            required
                        />

                        <button type="button"
                                @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5 focus:outline-none">

                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>

                            <svg x-show="show" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Type d'activité</h2>
                <p class="text-sm text-gray-500 mb-6">Sélectionnez la catégorie qui correspond le mieux à votre métier pour adapter l'interface.</p>

                <div class="grid gap-4">
                    <label class="relative flex p-4 cursor-pointer border rounded-lg hover:bg-blue-50 transition">
                        <input type="radio" name="type_metier" value="Batiment" class="mt-1 text-blue-600" required>
                        <div class="ml-3">
                            <span class="block text-sm font-bold text-gray-900">Bâtiment / Construction</span>
                            <span class="block text-xs text-gray-500">Devis avec surfaces (m²) et métrés.</span>
                        </div>
                    </label>

                    <label class="relative flex p-4 cursor-pointer border rounded-lg hover:bg-blue-50 transition">
                        <input type="radio" name="type_metier" value="Service" class="mt-1 text-blue-600">
                        <div class="ml-3">
                            <span class="block text-sm font-bold text-gray-900">Prestation de Services</span>
                            <span class="block text-xs text-gray-500">Facturation au temps passé (Heures).</span>
                        </div>
                    </label>

                    <label class="relative flex p-4 cursor-pointer border rounded-lg hover:bg-blue-50 transition">
                        <input type="radio" name="type_metier" value="Vente" class="mt-1 text-blue-600">
                        <div class="ml-3">
                            <span class="block text-sm font-bold text-gray-900">Commerce / Vente</span>
                            <span class="block text-xs text-gray-500">Gestion par quantités unitaires.</span>
                        </div>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('type_metier')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-between mt-8">
            <a class="text-sm text-gray-600 underline hover:text-gray-900" href="{{ route('login') }}">
                Déjà inscrit ?
            </a>

            <x-primary-button class="ml-4 bg-blue-600 hover:bg-blue-700">
                Créer mon compte
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
