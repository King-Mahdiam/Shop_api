<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id' ,
        'status' ,
        'total_amount' ,
        'delivery_amount' ,
        'paying_amount' ,
        'payment_status'
    ];
}
