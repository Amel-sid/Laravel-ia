<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('sector')->nullable();
            $table->integer('employees_count')->nullable();
            $table->json('scores'); // Stockage des scores par domaine
            $table->integer('total_score');
            $table->enum('status', ['in_progress', 'completed']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assessments');
    }
};
