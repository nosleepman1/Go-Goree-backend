<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerte_fraudes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payement_id')->nullable()->constrained('payements')->nullOnDelete();
            $table->string('niveau');
            $table->string('regle_declenchee');
            $table->json('payload_suspect')->nullable();
            $table->foreignUuid('traite_par')->nullable()->constrained('users')->nullOnDelete();
            $table->string('statut')->default('EN_ATTENTE');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerte_fraudes');
    }
};
