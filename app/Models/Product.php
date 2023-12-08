<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'image', 'rating'];
    protected $table = 'products';


    public function getVariants() {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }
}
