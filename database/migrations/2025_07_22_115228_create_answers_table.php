<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->integer('selected_option'); // 0, 1, 2, 3
            $table->integer('points_earned');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('answers');
    }
};
