<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'address_id', 'driver_id', 'subtotal', 'discount_amount', 'total_price', 'status', 'payment_method', 'delivery_photo_url', 'coupon_id'];
    protected $appends = ['full_delivery_photo_url'];

    public function getFullDeliveryPhotoUrlAttribute()
    {
        return $this->delivery_photo_url ? asset('storage/' . $this->delivery_photo_url) : null;
    }

    public function items() {
        return $this->hasMany(OrderItem::class);
    }
    public function address() {
        return $this->belongsTo(Address::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function driver() {
        return $this->belongsTo(User::class, 'driver_id');
    }
    public function coupon() {
        return $this->belongsTo(Coupon::class);
    }
}