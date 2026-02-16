<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price_bs',
        'price_usd',
        'stock',
        'active',
    ];
}
