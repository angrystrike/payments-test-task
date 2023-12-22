<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'order_id',
        'amount',
        'currency',
        'status',
        'order_created_at',
        'order_completed_at',
        'refunded_amount',
        'provision_amount',
        'hash',
        'is_cash',
        'send_push',
        'processing_time',
    ];
}
