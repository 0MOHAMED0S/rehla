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
        Schema::create('terms_of_uses', function (Blueprint $table) {
            $table->id();
            $table->string('main_title');
            $table->string('main_subtitle');
            $table->string('section1_title');
            $table->text('section1_text');
            $table->string('section2_title');
            $table->text('section2_text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms_of_uses');
    }
};
