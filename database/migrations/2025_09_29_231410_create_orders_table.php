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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->foreignId('children_id')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
        $table->string('image1');
        $table->string('image2');
        $table->string('image3');
        $table->text('child_attributes');
        $table->text('educational_goal');
        $table->decimal('price', 10, 2);
        $table->enum('price_type', [
                'electronic_copy_price',
                'fixed_price',
                'printed_copy_price',
                'offered_price'
            ])->nullable();
        $table->foreignId('shipping_id')->constrained('shippings')->cascadeOnDelete();
        $table->text('address');
        $table->string('phone', 20);
        $table->integer('age');
        $table->enum('gender', ['male', 'female']);
        $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
        $table->string('paymob_order_id')->nullable();
        $table->text('note')->nullable()->default(null);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
