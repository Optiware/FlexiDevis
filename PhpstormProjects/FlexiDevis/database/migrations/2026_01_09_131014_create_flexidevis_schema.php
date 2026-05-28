<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. UTILISATEURS
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_utilisateur');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'utilisateur'])->default('utilisateur');
            $table->enum('type_metier', ['Batiment', 'Service', 'Vente', 'Standard'])->default('Standard');
            $table->string('name')->nullable();
            $table->string('raison_sociale')->nullable();
            $table->string('email_contact')->nullable();
            $table->string('siret', 20)->nullable();
            $table->text('adresse')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('ville')->nullable();
            $table->string('telephone', 20)->nullable();
            $table->text('mentions_legales')->nullable();
            $table->string('logo')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. CLIENTS
        Schema::create('clients', function (Blueprint $table) {
            $table->id('id_client');
            $table->foreignId('id_utilisateur')->constrained('users', 'id_utilisateur')->onDelete('cascade');
            $table->string('nom')->nullable();
            $table->string('prenom')->nullable();
            $table->string('raison_sociale')->nullable();
            $table->text('adresse')->nullable();
            $table->string('code_postal', 10)->nullable();
            $table->string('ville')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('siret', 20)->nullable();
            $table->timestamps();
        });

        // 3. DOCUMENTS
        Schema::create('documents', function (Blueprint $table) {
            $table->id('id_document');
            $table->foreignId('id_client')->constrained('clients', 'id_client')->onDelete('cascade');
            $table->foreignId('id_utilisateur')->constrained('users', 'id_utilisateur')->onDelete('cascade');
            $table->enum('type_document', ['Devis', 'Facture']);
            $table->string('numero', 50)->unique();
            $table->date('date_emission');
            $table->date('date_echeance')->nullable();
            $table->enum('statut', ['Brouillon', 'Valide', 'Accepte', 'Refuse', 'Paye', 'Impaye'])->default('Brouillon');
            $table->decimal('total_ht', 10, 2)->default(0);
            $table->decimal('total_tva', 10, 2)->default(0);
            $table->decimal('total_ttc', 10, 2)->default(0);
            $table->text('conditions_reglement')->nullable();
            $table->text('notes')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('design_template')->default('classic');
            $table->timestamps();
        });

        // 4. LIGNE DOCUMENTS
        Schema::create('ligne_documents', function (Blueprint $table) {
            $table->id('id_ligne');
            $table->foreignId('id_document')->constrained('documents', 'id_document')->onDelete('cascade');
            $table->integer('ordre')->default(0);
            $table->text('description')->nullable();
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('quantite', 10, 2)->default(1);
            $table->decimal('surface_m2', 10, 2)->nullable();
            $table->decimal('temps_heures', 10, 2)->nullable();
            $table->string('unite_mesure', 20)->default('unite');
            $table->decimal('taux_tva', 5, 2)->default(20.0);
            $table->decimal('remise_percent', 5, 2)->default(0);
            $table->decimal('montant_ht', 10, 2);
            $table->decimal('montant_tva', 10, 2);
            $table->decimal('montant_ttc', 10, 2);
            $table->timestamps();
        });

        // 5. SIGNATURES
        Schema::create('signatures', function (Blueprint $table) {
            $table->id('id_signature');
            $table->foreignId('id_document')->unique()->constrained('documents', 'id_document')->onDelete('cascade');
            $table->longText('signature_data');
            $table->string('ip_client', 45)->nullable();
            $table->string('nom_signataire')->nullable();
            $table->dateTime('date_signature')->useCurrent();
            $table->timestamps();
        });

        // 6. CACHE
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('signatures');
        Schema::dropIfExists('ligne_documents');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
