<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('assessment_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type'); // 'post_diagnostic' ou 'direct'
            $table->string('step'); // 'document_selection', 'gathering_info', etc.
            $table->json('context'); // Stockage des réponses et état
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};
