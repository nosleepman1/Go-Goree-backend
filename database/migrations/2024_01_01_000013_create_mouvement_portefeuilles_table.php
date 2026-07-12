<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mouvement_portefeuilles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('montant', 12, 2);
            $table->string('type');
            $table->foreignUuid('payement_id')->nullable()->constrained('payements')->nullOnDelete();
            $table->string('statut')->default('EN_ATTENTE');
            $table->foreignUuid('portefeuille_id')->constrained('portefeuilles')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mouvement_portefeuilles');
    }
};
