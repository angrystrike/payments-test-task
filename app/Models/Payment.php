<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'currency_id',
        'order_id',
        'amount',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
