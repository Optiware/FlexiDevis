<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Alexis Admin',
            'email' => 'alexis@flexidevis.com',
            'password' => Hash::make('password'),

            'raison_sociale' => 'FlexiDevis Studio',
            'adresse' => '12 Avenue du Code, 69000 Lyon',
            'code_postal' => '69000',
            'ville' => 'Lyon',
            'email_contact' => 'contact@flexidevis.com',
            'telephone' => '06 12 34 56 78',
            'siret' => '800 123 456 00018',
        ]);

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
    }
}
