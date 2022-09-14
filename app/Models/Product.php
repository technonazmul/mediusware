<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];

    /**
     * Get the variants for the blog post.
     */
    public function ProductVariantPrice()
    {
        return $this->hasMany('App\Models\ProductVariantPrice');
    }
}
