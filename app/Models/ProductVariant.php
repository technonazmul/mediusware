<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    public function productVariantPrices()
    {
        return $this->hasMany('App\Models\ProductVariantPrice');
    }
}
