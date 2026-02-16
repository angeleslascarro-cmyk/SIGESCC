<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Neighbor extends Model
{
    protected $fillable = [
  'full_name','cedula','phone','address',
  'credit_limit_bs','credit_limit_usd',
];


    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }
}
