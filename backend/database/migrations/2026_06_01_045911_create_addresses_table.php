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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ភ្ជាប់ទៅម្ចាស់អាសយដ្ឋាន
            $table->string('title'); // ចំណងជើង (ឧ. ផ្ទះ, កន្លែងធ្វើការ)
            $table->string('phone_number'); // លេខទូរស័ព្ទសម្រាប់ទីតាំងនេះ
            $table->text('address_line'); // លេខផ្ទះ ផ្លូវ សង្កាត់...
            $table->string('city_province')->nullable(); // ខេត្ត/ក្រុង
            $table->boolean('is_default')->default(false); // កំណត់ជាអាសយដ្ឋានគោល (True/False)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
