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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete(); // ទីតាំងដឹកជញ្ជូន
            $table->decimal('total_price', 10, 2); // តម្លៃសរុប
            $table->string('status')->default('pending'); // ស្ថានភាព: pending, processing, shipped, delivered, cancelled
            $table->string('payment_method')->default('cod'); // cod (Cash on Delivery) ឬ aba
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
