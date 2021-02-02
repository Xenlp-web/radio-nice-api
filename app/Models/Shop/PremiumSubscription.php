<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PremiumSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_in_months',
        'price'
    ];
}
