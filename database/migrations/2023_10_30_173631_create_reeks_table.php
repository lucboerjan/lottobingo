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
        Schema::create('reeks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('reeksID');
            $table->unsignedInteger('g1');
            $table->unsignedInteger('g2');
            $table->unsignedInteger('g3');
            $table->unsignedInteger('g4');
            $table->unsignedInteger('g5');
            $table->unsignedInteger('g6');
            $table->unsignedInteger('g7');
            $table->unsignedInteger('g8');
            $table->unsignedInteger('g9');
            $table->unsignedInteger('g10');
            $table->boolean('obsolete');
            $table->unsignedInteger('aantalJuist');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reeks');
    }
};
