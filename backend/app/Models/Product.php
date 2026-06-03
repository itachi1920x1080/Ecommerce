<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * កំណត់ឈ្មោះឈរ (Columns) ដែលអនុញ្ញាតឲ្យបញ្ចូលទិន្នន័យបាន (Mass Assignment)
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'discount_percent',
        'stock',
        'image_url',
        'category_id', // <--- បានបន្ថែមទីតាំងនេះសម្រាប់ភ្ជាប់ជាមួយ Category រួចរាល់
    ];

    // ==========================================
    // 🟢 មុខងារបន្ថែមថ្មី៖ បង្កើត Full Link សម្រាប់រូបភាព
    // ==========================================
    protected $appends = ['full_image_url'];

    public function getFullImageUrlAttribute()
    {
        if ($this->image_url) {
            // បង្កើត Link ពេញលេញ (ឧ. http://localhost:8000/storage/products/xxx.jpg)
            return asset('storage/' . $this->image_url);
        }
        // បើអត់ទាន់មានរូបភាព ប្រើរូបភាព Default នេះសិន
        return 'https://via.placeholder.com/150?text=No+Image'; 
    }
    // ==========================================

    /**
     * ទំនាក់ទំនង៖ ទំនិញនេះស្ថិតនៅក្នុង Category មួយណា?
     * (ទំនាក់ទំនងប្រភេទ Belongs To)
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * ទំនាក់ទំនង៖ ទំនិញនេះមាននៅក្នុងកន្ត្រកអ្នកណាខ្លះ?
     * (ទំនាក់ទំនងប្រភេទ Has Many)
     */
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * ទំនាក់ទំនង៖ ទំនិញនេះមាននៅក្នុងវិក្កយបត្រ (Order) ណាខ្លះ?
     * (ទំនាក់ទំនងប្រភេទ Has Many តាមរយៈ OrderItem)
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    
    /**
     * ទំនាក់ទំនង៖ ទំនិញនេះមានមតិយោបល់ (Reviews) អ្វីខ្លះ?
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    
    /**
     * ទំនាក់ទំនង៖ ទំនិញមួយអាចមានច្រើនជម្រើស (Variants)
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}