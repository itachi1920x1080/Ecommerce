<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    //
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price', // តម្លៃពេលទិញភ្លាមៗ
    ];

    // ទំនាក់ទំនង៖ ភ្ជាប់ទៅយកព័ត៌មានទំនិញ (ឈ្មោះ រូបភាព...)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
