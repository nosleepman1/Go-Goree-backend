<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voyages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('date_voyage');
            $table->integer('places');
            $table->integer('places_restantes');
            $table->foreignUuid('trajet_id')->constrained('trajets')->restrictOnDelete();
            $table->foreignUuid('chaloupe_id')->constrained('chaloupes')->restrictOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voyages');
    }
};
