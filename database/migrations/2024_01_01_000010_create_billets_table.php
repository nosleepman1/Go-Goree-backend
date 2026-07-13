<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('qr_token')->unique();
            $table->decimal('montant', 10, 2);
            $table->string('statut')->default('EN_ATTENTE_PAIEMENT');
            $table->foreignUuid('voyage_id')->constrained('voyages')->restrictOnDelete();
            // Nullable : un billet « gratuit » d'abonné peut ne référencer aucun tarif.
            $table->foreignUuid('tarif_id')->nullable()->constrained('tarifs')->nullOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Au plus un billet ACTIF par (client, voyage) — garanti au niveau DB
        // (empêche la course entre deux achats simultanés).
        DB::statement(
            'CREATE UNIQUE INDEX billets_user_voyage_actif_unique ON billets (user_id, voyage_id) '.
            "WHERE statut IN ('EN_ATTENTE_PAIEMENT','PAYE','UTILISE') AND deleted_at IS NULL"
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('billets');
    }
};
