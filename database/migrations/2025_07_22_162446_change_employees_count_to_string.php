<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assessments', function (Blueprint $table) {
            // Changer de integer vers string
            $table->string('employees_count')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('assessments', function (Blueprint $table) {
            // Retour vers integer si besoin
            $table->integer('employees_count')->nullable()->change();
        });
    }
};
