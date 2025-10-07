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
        Schema::table('documents', function (Blueprint $table) {
            // 1. Permettre user_id nullable (pour les documents anonymes)
            $table->foreignId('user_id')->nullable()->change();

            // 2. Supprimer l'index existant puis la colonne status
            $table->dropIndex(['status']); // Supprimer l'index avant de dropper la colonne
            $table->dropColumn('status');
        });

        // Recréer la colonne status avec les nouvelles valeurs
        Schema::table('documents', function (Blueprint $table) {
            $table->enum('status', ['anonymous', 'draft', 'generated', 'reviewed', 'approved', 'outdated'])
                ->default('anonymous')
                ->index()
                ->after('type');
        });

        Schema::table('documents', function (Blueprint $table) {
            // 3. Ajouter les champs pour les documents anonymes
            $table->string('preview_token', 64)->nullable()->unique()->after('parent_document_id');
            $table->timestamp('token_expires_at')->nullable()->after('preview_token');

            // 4. Ajouter les index manquants
            $table->index('preview_token');
            $table->index('token_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Supprimer les nouveaux champs
            $table->dropIndex(['preview_token']);
            $table->dropIndex(['token_expires_at']);
            $table->dropColumn(['preview_token', 'token_expires_at']);

            // Remettre user_id obligatoire
            $table->foreignId('user_id')->nullable(false)->change();
        });

        // Restaurer l'ancien enum status
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->enum('status', ['draft', 'generated', 'reviewed', 'approved', 'outdated'])
                ->default('generated')
                ->index()
                ->after('type');
        });
    }
};
