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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            // កូដសម្រាប់វាយបញ្ជូល (ឧ. RUPP50) -> ត្រូវតែពិសេសមិនជាន់គ្នា
            $table->string('code')->unique(); 
            // ប្រភេទបញ្ចុះតម្លៃ (គិតជាភាគរយ 'percent' ឬ គិតជាលុយផ្ទាល់ 'fixed')
            $table->enum('type', ['percent', 'fixed']); 
            // ចំនួនដែលត្រូវបញ្ចុះ (ឧ. បើប្រភេទ percent លេខ 10 គឺបញ្ចុះ 10% / បើ fixed លេខ 10 គឺបញ្ចុះ 10$)
            $table->decimal('value', 8, 2); 
            // ថ្ងៃផុតកំណត់ (អាចមាន ឬគ្មានកំណត់ក៏បាន)
            $table->dateTime('expires_at')->nullable(); 
            // បិទ/បើក ឱ្យប្រើប្រាស់ (1 = ប្រើបាន, 0 = ផ្អាក)
            $table->boolean('is_active')->default(true); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
