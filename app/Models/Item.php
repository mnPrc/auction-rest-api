<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'start_price',
        'buy_now_price',
        'payment',
        'delivery',
        'end_time',
        'buyer_id',
        'buy_price',
        'active'
];
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function buyer(){
        return $this->hasOne(User::class, 'buyer_id');
    }
}

