<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        // added
        'transaction_id',
    ];

    protected $table = 'orders';

    protected $casts = [
        'order_items_id' => 'array',
    ];
}
