<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('domain'); // gouvernance, access, protection, continuity
            $table->string('code'); // A1, A2, B1, B2, etc.
            $table->text('question');
            $table->json('options'); // Les 4 options de réponse avec leurs points
            $table->integer('max_points');
            $table->integer('order');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
};
