<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\PromoCode;
use App\Models\UserPromoCode;
use App\Models\User;
use App\Models\Card;
use App\Models\Currency;
use App\Helpers\CurrencyConverter;
use App\Interfaces\CardServiceInterface;


/**
 * Class CardService
 *
 * A service class for managing card-related operations.
 */
class CardService implements CardServiceInterface 
{
    private $currencyConverter;

    public function __construct()
    {
        $this->currencyConverter = new CurrencyConverter();
    }

    /**
     * Store a card with associated payment and promotional data.
     *
     * @param array  $data      The payment data.
     * @param string $email     The email of the user.
     * @param array  $promoData The promotional data.
     */
    public function store($data, $email, $promoData)
    {
        // Only handle completed transations
        if (!in_array($data['Payment']['status'], ['completed'])) {
            return;
        }

        // Convert the payment amount to the default currency USD.
        $total = $this->currencyConverter->convert($data['Payment']['amount'], $data['Payment']['currency']);

        // If promotional data is provided, convert and add the bonus to the total.
        if ($promoData) {
            $bonus = $this->currencyConverter->convert($promoData['bonus'], $promoData['currency']);
            $total += $bonus;
        }

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