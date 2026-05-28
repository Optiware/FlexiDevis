<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // On ne vide QUE la table users pour l'instant
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Table users vidée avec succès.');

        // 1. Ton compte Admin
        User::create([
            'name' => 'Alexis Admin',
            'email' => 'alexis@flexidevis.com',
            'password' => Hash::make('password'),
            'raison_sociale' => 'FlexiDevis Studio',
            'adresse' => '12 Avenue du Code',
            'code_postal' => '69000',
            'ville' => 'Lyon',
            'email_contact' => 'contact@flexidevis.com',
            'telephone' => '06 12 34 56 78',
            'siret' => '800 123 456 00018',
        ]);

        // 2. Le compte de test
        User::create([
            'name' => 'Jean Testeur',
            'email' => 'jean@test.com',
            'password' => Hash::make('password'),
            'raison_sociale' => 'Jean Bricolage EURL',
            'adresse' => '5 Rue du Test',
            'code_postal' => '75001',
            'ville' => 'Paris',
            'siret' => '900 987 654 00022',
        ]);

        // 3. Trois autres comptes aléatoires
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'name' => $faker->name(),
                'email' => "commercial{$i}@flexidevis.com",
                'password' => Hash::make('password'),
                'raison_sociale' => $faker->company(),
                'adresse' => $faker->streetAddress(),
                'code_postal' => str_replace(' ', '', $faker->postcode()),
                'ville' => $faker->city(),
                'telephone' => $faker->phoneNumber(),
                'siret' => $faker->numerify('### ### ### #####'),
            ]);
        }

        $this->command->info('✅ Les 5 Utilisateurs sont créés ! Tu peux te connecter.');
    }
}
