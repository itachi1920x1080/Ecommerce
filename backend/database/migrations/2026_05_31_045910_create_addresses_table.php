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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // ភ្ជាប់ជាមួយ User
            $table->string('receiver_name'); // ឈ្មោះអ្នកទទួលអីវ៉ាន់
            $table->string('phone_number'); // លេខទូរស័ព្ទអ្នកទទួល
            $table->text('full_address'); // អាសយដ្ឋានពេញលេញ (ផ្ទះលេខ ផ្លូវ សង្កាត់...)
            $table->string('city')->default('Phnom Penh'); // ខេត្ត/ក្រុង
            $table->boolean('is_default')->default(false); // កំណត់ថាជាអាសយដ្ឋានចម្បងឬអត់
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
