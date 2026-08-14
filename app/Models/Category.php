<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'image',
        'description',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function getImageUrlAttribute()
    {
        return imageUrl($this->image, 'categories');
    }
} 