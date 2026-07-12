<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->unique();
            $table->decimal('montant', 10, 2);
            $table->string('statut')->default('EN_COURS');
            $table->string('mode');
            $table->string('type_transaction')->nullable();
            $table->string('paydunya_token')->nullable();
            $table->foreignUuid('billet_id')->nullable()->constrained('billets')->nullOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payements');
    }
};
