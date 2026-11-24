<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Document;
use App\Models\Product;
use App\Models\User;
use App\Services\CalculDocument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Données de démonstration (inventées), rejouables sans doublon et sur toute base
 * (SQLite, MySQL). Comptes : alexis@flexidevis.com et jean@test.com, mot de passe « password ».
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $alexis = User::updateOrCreate(['email' => 'alexis@flexidevis.com'], [
            'name' => 'Alexis Admin',
            'password' => Hash::make('password'),
            'type_metier' => 'Batiment',
            'raison_sociale' => 'FlexiDevis Studio',
            'adresse' => '12 avenue du Code',
            'code_postal' => '69000',
            'ville' => 'Lyon',
            'email_contact' => 'contact@flexidevis.com',
            'telephone' => '06 12 34 56 78',
            'siret' => '800 123 456 00018',
        ]);

        User::updateOrCreate(['email' => 'jean@test.com'], [
            'name' => 'Jean Testeur',
            'password' => Hash::make('password'),
            'raison_sociale' => 'Jean Bricolage EURL',
            'adresse' => '5 rue du Test',
            'code_postal' => '75001',
            'ville' => 'Paris',
            'siret' => '900 987 654 00022',
        ]);

        if ($alexis->documents()->exists()) {
            return;
        }

        $clients = collect([
            ['nom' => 'Durand', 'prenom' => 'Sophie', 'raison_sociale' => null, 'ville' => 'Villeurbanne', 'code_postal' => '69100', 'email' => 'sophie.durand@example.test'],
            ['nom' => 'Lambert', 'prenom' => 'Marc', 'raison_sociale' => 'Boulangerie Lambert', 'ville' => 'Lyon', 'code_postal' => '69003', 'email' => 'contact@boulangerie-lambert.example.test'],
            ['nom' => 'Nguyen', 'prenom' => 'Linh', 'raison_sociale' => 'Studio Linh Archi', 'ville' => 'Caluire', 'code_postal' => '69300', 'email' => 'linh@studio-archi.example.test'],
        ])->map(fn ($client) => Client::create(['id_utilisateur' => $alexis->getKey(), 'adresse' => '1 rue de l’Exemple', ...$client]));

        foreach ([
            ['CAR-01', 'Carrelage grès cérame 60×60', 32.50, 20, 140, 30],
            ['PEI-02', 'Peinture murale blanche 10 L', 54.90, 20, 12, 5],
            ['MO-01', 'Main-d’œuvre (heure)', 45.00, 10, 0, 0],
            ['PLA-03', 'Plaque de plâtre BA13', 8.20, 20, 3, 10],
        ] as [$reference, $designation, $prix, $tva, $stock, $seuil]) {
            Product::create([
                'id_utilisateur' => $alexis->getKey(),
                'reference' => $reference,
                'designation' => $designation,
                'prix_ht' => $prix,
                'tva' => $tva,
                'stock_actuel' => $stock,
                'seuil_alerte' => $seuil,
            ]);
        }

        $annee = date('Y');
        $documents = [
            [$clients[0], 'Devis', "D-$annee-0001", 'Valide', 5, [
                ['description' => 'Rénovation salle de bain : dépose et pose carrelage', 'quantite' => 12, 'prix_unitaire' => 32.5, 'tva' => 10],
                ['description' => 'Main-d’œuvre', 'quantite' => 16, 'prix_unitaire' => 45, 'tva' => 10],
            ]],
            [$clients[1], 'Facture', "F-$annee-0001", 'Paye', 0, [
                ['description' => 'Peinture de la boutique', 'quantite' => 3, 'prix_unitaire' => 54.9, 'tva' => 20],
                ['description' => 'Main-d’œuvre', 'quantite' => 8, 'prix_unitaire' => 45, 'tva' => 20, 'remise_percent' => 10],
            ]],
            [$clients[2], 'Devis', "D-$annee-0002", 'Brouillon', 0, [
                ['description' => 'Cloison en plaques de plâtre BA13', 'quantite' => 24, 'prix_unitaire' => 8.2, 'tva' => 20],
                ['description' => 'Main-d’œuvre', 'quantite' => 6, 'prix_unitaire' => 45, 'tva' => 20],
            ]],
        ];

        foreach ($documents as [$client, $type, $numero, $statut, $remise, $lignes]) {
            $calcul = CalculDocument::calculer($lignes, $remise);
            $document = Document::create([
                'id_client' => $client->id_client,
                'id_utilisateur' => $alexis->getKey(),
                'type_document' => $type,
                'numero' => $numero,
                'date_emission' => now()->subDays(10)->toDateString(),
                'date_echeance' => now()->addDays(20)->toDateString(),
                'statut' => $statut,
                'remise_globale' => $remise,
                'total_ht' => $calcul['total_ht'],
                'total_tva' => $calcul['total_tva'],
                'total_ttc' => $calcul['total_ttc'],
            ]);
            foreach ($calcul['lignes'] as $ligne) {
                $document->lignes()->create($ligne);
            }
        }
    }
}
