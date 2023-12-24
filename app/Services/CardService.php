<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\PromoCode;
use App\Models\UserPromoCode;
use App\Models\User;
use App\Models\Card;
use App\Models\Currency;
use App\Interfaces\CardServiceInterface;


class CardService implements CardServiceInterface 
{
    public function store($data, $email, $bonus)
    {
        $total = $data['Payment']['amount'] + $bonus;
        $user = User::where('email', $email)->first();
        $card = Card::where('user_id', $user->id)->first();

        if (!$card) {
            $usd = Currency::where('title', 'USD')->first();
            $data['Card']['currency_id'] = $usd->id;
            $data['Card']['balance'] = $total;
            $data['Card']['user_id'] = $user->id;
            $card = Card::create($data['Card']);
        } else {
            $card->update([
                'balance' => $card->balance + $total,
            ]);
        }
    }
}