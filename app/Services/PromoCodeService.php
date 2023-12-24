<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\PromoCode;
use App\Models\UserPromoCode;
use App\Models\User;
use App\Models\Card;
use App\Interfaces\PromoCodeServiceInterface;


class PromoCodeService implements PromoCodeServiceInterface {
    
    // $user = User::where('email', $data['Payment']['email'])->first();
    // $card = Card::where('user_id', $user->id)->first();
    // $promoCode = PromoCode::where('code', $promoCode)->first();
    // $userPromoCode = null;
    // $amount = $data['Payment']['amount'];
    // if ($promoCode) {
    //     $userPromoCode = UserPromoCode::where([
    //         'user_id' => $user->id,
    //         'promo_code_id' => $promoCode->id
    //     ])->first();
    // }
    // if (!$userPromoCode) {
    //     UserPromoCode::create([
    //         'user_id' => $user->id,
    //         'promo_code_id' => $promoCode->id
    //     ]);
    //     $amount += $promoCode->bonus_amount;
    // }

    public function store($code, $email)
    {
        $user = User::where('email', $email)->first();
        $promoCode = PromoCode::where('code', $code)->first();
        $userPromoCode = null;
        $bonus = 0;
        if ($promoCode) {
            $userPromoCode = UserPromoCode::where([
                'user_id' => $user->id,
                'promo_code_id' => $promoCode->id
            ])->first();
        }
        if (!$userPromoCode) {
            UserPromoCode::create([
                'user_id' => $user->id,
                'promo_code_id' => $promoCode->id
            ]);
            $bonus = $promoCode->bonus_amount;
        }

        return $bonus;
    }
}