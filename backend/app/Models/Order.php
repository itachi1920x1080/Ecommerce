<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
    ];

    // ទំនាក់ទំនង៖ វិក្កយបត្រនេះមានទំនិញអ្វីខ្លះ?
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
