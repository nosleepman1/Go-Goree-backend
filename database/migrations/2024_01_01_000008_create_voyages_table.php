<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

        // Au plus un voyage par (trajet, date) — évite les doublons de génération.
        DB::statement(
            'CREATE UNIQUE INDEX voyages_trajet_date_unique ON voyages (trajet_id, date_voyage) '.
            'WHERE deleted_at IS NULL'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('voyages');
    }
};
