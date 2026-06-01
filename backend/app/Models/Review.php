<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'product_id', 'rating', 'comment'];

    // Review នេះជារបស់ User ណា?
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Review នេះសម្រាប់ Product ណា?
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}