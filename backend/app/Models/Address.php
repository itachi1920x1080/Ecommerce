<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'phone_number', 'address_line', 'city_province', 'is_default'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}