<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'product_variant_id', 'quantity', 'price'];

    // ទំនាក់ទំនង៖ OrderItem នីមួយៗគឺជារបស់ Product មួយ
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ទំនាក់ទំនង៖ OrderItem អាចមានភ្ជាប់ជម្រើស Variant មកជាមួយ
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}