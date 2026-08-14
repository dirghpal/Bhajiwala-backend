<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'stock',
        'unit',
        'image',
        'featured',
        'status'
    ];

    protected $casts = [
        'featured' => 'boolean',
        'status' => 'boolean',
    ];

    protected $appends = [
        'image_url'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute()
    {
        return imageUrl($this->image,'products');
    }
}