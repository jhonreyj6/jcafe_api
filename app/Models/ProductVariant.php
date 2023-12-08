<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'value', 'unit', 'price', 'stock', 'stripe_api_id'];
    protected $table = 'product_variants';
}
