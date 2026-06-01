<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
    ];

    // ១. ទំនាក់ទំនង៖ វិក្កយបត្រនេះជារបស់អ្នកណា (User)?
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ២. ទំនាក់ទំនង៖ វិក្កយបត្រនេះមានទំនិញអ្វីខ្លះ (Items)?
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}