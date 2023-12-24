<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Models\Card;
use App\Models\PromoCode;
use App\Models\UserPromoCode;
use App\Services\PaymentService;
use App\Helpers\CurrencyConverter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Interfaces\PaymentServiceInterface;
use App\Interfaces\PromoCodeServiceInterface;
use App\Interfaces\CardServiceInterface;
use Illuminate\Support\Facades\App;


class PaymentController extends Controller
{
    private $paymentService;
    private $promoCodeService;
    private $cardService;

    public function __construct()
    {
        $this->paymentService = App::make(PaymentServiceInterface::class);
        $this->promoCodeService = App::make(PromoCodeServiceInterface::class);
        $this->cardService = App::make(CardServiceInterface::class);
    }

    public function store(Request $request, $promoCode = null)
    {
        $transactionData = $this->paymentService->store($request->all());
        $email = $transactionData['Payment']['email'];

        $bonus = $this->promoCodeService->store($promoCode, $email);
        $this->cardService->store($transactionData, $email, $bonus);

        return response()->json(123);
    }
}