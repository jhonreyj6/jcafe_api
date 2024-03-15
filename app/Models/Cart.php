<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariant;
use App\Models\Product;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'carts';
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'product_variant_id',
        'created_at',
    ];

    public function getVariants()
    {
        return $this->hasOne(ProductVariant::class, 'id', 'product_variant_id');
    }

    public function getProductDetails()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
