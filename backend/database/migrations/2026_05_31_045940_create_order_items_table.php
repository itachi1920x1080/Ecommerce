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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // ស្ថិតក្នុងវិក្កយបត្រមួយណា?
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // ទិញទំនិញអ្វី?
            $table->integer('quantity'); // ចំនួនប៉ុន្មាន?
            $table->decimal('price', 10, 2); // តម្លៃទំនិញនៅពេលកំពុងទិញ (ការពារថ្ងៃក្រោយទំនិញឡើងថ្លៃ)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
