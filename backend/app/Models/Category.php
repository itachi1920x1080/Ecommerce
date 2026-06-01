<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    // ទំនាក់ទំនង៖ Category មួយ មានផ្ទុកទំនិញ (Products) ច្រើន
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}