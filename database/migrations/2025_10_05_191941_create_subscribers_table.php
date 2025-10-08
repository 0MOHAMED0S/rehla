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
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('children_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->enum('gender', ['male', 'female']);
            $table->integer('age');
            $table->string('image1');
            $table->string('image2');
            $table->string('image3');
            $table->text('child_attributes');
            $table->text('educational_goal');
            $table->decimal('price', 10, 2);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('shipping_id')->constrained('shippings')->cascadeOnDelete();
            $table->text('address');
            $table->string('phone', 20);
            $table->enum('status', ['subscribed', 'failed', 'expired','pending'])->default('pending');
            $table->string('paymob_order_id')->nullable();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscribers');
    }
};
