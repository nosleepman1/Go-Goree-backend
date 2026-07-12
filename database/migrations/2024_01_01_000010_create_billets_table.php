<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            $table->foreignUuid('tarif_id')->constrained('tarifs')->restrictOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billets');
    }
};
