<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Session d'embarquement : un contrôleur « ouvre » l'embarcation d'un voyage,
     * puis les scans se rattachent à cette session (et donc à ce voyage).
     */
    public function up(): void
    {
        Schema::create('embarquements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('statut')->default('OUVERT');
            $table->timestamp('ouvert_a')->nullable();
            $table->timestamp('ferme_a')->nullable();
            $table->foreignUuid('voyage_id')->constrained('voyages')->cascadeOnDelete();
            $table->foreignUuid('ouvert_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('embarquements');
    }
};
