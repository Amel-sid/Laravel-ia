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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            // Relations (avec contraintes car les tables existent)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('assessment_id')->nullable()->constrained()->onDelete('set null');

            // Informations de base
            $table->string('title');
            $table->enum('type', ['pssi', 'charte', 'sauvegarde', 'incident', 'audit'])->index();
            $table->enum('status', ['draft', 'generated', 'reviewed', 'approved', 'outdated'])->default('generated')->index();

            // Contenu
            $table->longText('content');
            $table->json('metadata')->nullable(); // Réponses, paramètres IA, etc.

            // Versioning
            $table->string('version', 10)->default('1.0');
            $table->foreignId('parent_document_id')->nullable()->constrained('documents')->onDelete('set null');

            // Tracking
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('last_reviewed_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            // SEO et recherche
            $table->text('excerpt')->nullable();
            $table->json('tags')->nullable();

            $table->timestamps();

            // Index pour les performances
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'status']);
            $table->index(['type', 'status']);
            $table->index('generated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
