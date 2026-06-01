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
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            // ភ្ជាប់ទៅកាន់ User ដែលជាម្ចាស់
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // ភ្ជាប់ទៅកាន់ទំនិញដែលគេចុចបេះដូង
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // ចំណុចសំខាន់៖ ការពារកុំឱ្យ User ម្នាក់ចុចបេះដូងលើទំនិញដដែល ២ដង (ស្ទួនទិន្នន័យ)
            $table->unique(['user_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
