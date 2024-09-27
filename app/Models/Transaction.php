<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id' ,
        'order_id' ,
        'amount' ,
        'token' ,
        'trans_id' ,
        'status' ,
        'request_from'
    ];
}
