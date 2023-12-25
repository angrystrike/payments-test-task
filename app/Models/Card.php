<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bin',
        'last_digits',
        'payment_system',
        'payment_group',
        'country',
        'currency_id',
        'balance'
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
