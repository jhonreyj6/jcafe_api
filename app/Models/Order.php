<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItems;
use App\Models\ProductVariant;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        // added
        'stripe_transaction_id',
    ];

    protected $table = 'orders';

    protected $casts = [
        'order_items_id' => 'array',
    ];

    public function getOrderItems() {
        return $this->hasMany(OrderItems::class, 'order_id');
    }
}
