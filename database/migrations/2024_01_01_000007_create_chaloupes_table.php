<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chaloupes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('imatriculation')->unique();
            $table->string('nom');
            $table->integer('capacite');
            $table->string('statut')->default('ACTIVE');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chaloupes');
    }
};
