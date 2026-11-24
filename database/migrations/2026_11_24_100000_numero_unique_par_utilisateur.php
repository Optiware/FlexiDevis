<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Le numéro (D-2026-0001…) était unique pour toute l'application : le premier devis d'un
 * deuxième utilisateur était refusé. Il devient unique par utilisateur.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropUnique(['numero']);
            $table->unique(['id_utilisateur', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropUnique(['id_utilisateur', 'numero']);
            $table->unique('numero');
        });
    }
};
