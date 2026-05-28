<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('id_produit'); // Clé primaire

            $table->foreignId('id_utilisateur')->constrained('users', 'id_utilisateur')->onDelete('cascade');

            $table->string('reference')->nullable();
            $table->string('designation');
            $table->text('description')->nullable();

            $table->decimal('prix_ht', 10, 2)->default(0);
            $table->decimal('tva', 5, 2)->default(20.00);


            $table->integer('stock_actuel')->default(0);
            $table->integer('seuil_alerte')->default(5);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
