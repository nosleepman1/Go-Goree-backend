<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('resultat');
            $table->foreignUuid('billet_id')->constrained('billets')->cascadeOnDelete();
            $table->foreignUuid('embarquement_id')->nullable()->constrained('embarquements')->nullOnDelete();
            $table->foreignUuid('scanne_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scans');
    }
};
