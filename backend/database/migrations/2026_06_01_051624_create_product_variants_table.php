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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            // ភ្ជាប់ទៅកាន់ទំនិញគោល (Product)
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); 
            
            // ឈ្មោះជម្រើស ឧទាហរណ៍៖ "ពណ៌ក្រហម", "ទំហំ XL", ឬ "256GB"
            $table->string('name'); 
            
            // តម្លៃជាក់លាក់សម្រាប់ជម្រើសនេះ (ព្រោះទំហំធំ ឬ Storage ធំ ច្រើនតែថ្លៃជាង)
            $table->decimal('price', 10, 2); 
            
            // ចំនួនស្តុកជាក់លាក់សម្រាប់ជម្រើសនេះ
            $table->integer('stock')->default(0); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
