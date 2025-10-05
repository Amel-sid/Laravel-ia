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
        Schema::table('users', function (Blueprint $table) {
            // Informations entreprise
            $table->string('company')->nullable()->after('email');
            $table->string('job_title')->nullable()->after('company');
            $table->enum('company_size', ['1-10', '11-50', '51-200', '201-500', '500+'])->nullable()->after('job_title');
            $table->string('sector')->nullable()->after('company_size');

            // Préférences utilisateur
            $table->json('preferences')->nullable()->after('sector');
            $table->string('timezone', 50)->default('Europe/Paris')->after('preferences');
            $table->string('language', 2)->default('fr')->after('timezone');

            // Statistiques utilisateur
            $table->integer('documents_generated')->default(0)->after('language');
            $table->decimal('avg_satisfaction', 3, 2)->nullable()->after('documents_generated');
            $table->timestamp('last_activity_at')->nullable()->after('avg_satisfaction');

            // Magic link (pour l'auth sans mot de passe)
            $table->boolean('prefers_magic_link')->default(false)->after('password');

            // Onboarding
            $table->boolean('onboarding_completed')->default(false)->after('last_activity_at');
            $table->json('onboarding_steps')->nullable()->after('onboarding_completed');

            // Index pour les performances
            $table->index('company_size');
            $table->index('sector');
            $table->index('last_activity_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer les index d'abord
            $table->dropIndex(['company_size']);
            $table->dropIndex(['sector']);
            $table->dropIndex(['last_activity_at']);

            // Puis supprimer les colonnes
            $table->dropColumn([
                'company',
                'job_title',
                'company_size',
                'sector',
                'preferences',
                'timezone',
                'language',
                'documents_generated',
                'avg_satisfaction',
                'last_activity_at',
                'prefers_magic_link',
                'onboarding_completed',
                'onboarding_steps'
            ]);
        });
    }
};
