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
        Schema::create('logo_and_links', function (Blueprint $table) {
            $table->id();
            $table->string('main_logo');
            $table->string('creative_writing_logo');
            $table->string('gate_inha_lak_image');
            $table->string('gate_start_journey_image');
            $table->string('about_page_image');
            $table->string('facebook_link');
            $table->string('twitter_link');
            $table->string('instagram_link');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logo_and_links');
    }
};
