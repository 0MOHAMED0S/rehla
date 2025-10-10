<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('price_equations', function (Blueprint $table) {
            $table->id();
             $table->decimal('base_price', 10, 2)->default(0); // e.g., 150
            $table->decimal('multiplier', 10, 2)->default(1); // e.g., 6.5
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_equations');
    }
};
