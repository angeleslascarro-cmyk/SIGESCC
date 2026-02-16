<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'neighbor_id',
        'user_id',
        'currency',
        'status',
        'total',
    ];

    public function neighbor()
    {
        return $this->belongsTo(\App\Models\Neighbor::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function items()
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }
}
