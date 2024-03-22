<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\OrderItems;
use App\Models\Product;

class ProductVariantController extends Controller
{
    public function show($id) {
        $order = OrderItems::where('order_id', $id)->get();

        return $order;
    }
}
