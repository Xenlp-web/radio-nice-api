<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionOrders extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'price',
        'status'
    ];
}
