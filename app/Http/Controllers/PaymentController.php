<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Models\Card;
use App\Services\PaymentService;
use App\Helpers\CurrencyConverter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class PaymentController extends Controller
{
    private PaymentService $paymentService;
    private CurrencyConverter $currencyConverter;

    public function __construct(PaymentService $paymentService, CurrencyConverter $currencyConverter)
    {
        $this->paymentService = $paymentService;
        $this->currencyConverter = $currencyConverter;
    }

    public function store(Request $request, $promoCode = null)
    {
        Log::info('Payment processed successfully');
        $statuses = [
            'completed' => ['complete', 'completed', 2],
            'pending' => ['pending', 'processing', 4],
            'refunded' => ['refunded'],
            'failed' => ['failed', 3]
        ];

        $test = $this->paymentService->test();
        $tes = $this->currencyConverter->test();

        $mapData = [
            'status' => [
                'keys' => ['state', 'status'],
                'values' => $statuses,
                'entity' => 'Payment'
            ],
            'transaction_id' => [
                'keys' => ['transactionId', 'identifier', 'txid'],
                'entity' => 'Payment'
            ],
            'order_id' => [
                'keys' => ['userOrderId', 'orderId', 'order'],
                'entity' => 'Payment'
            ],
            'currency' => [
                'keys' => ['currency'],
                'entity' => 'Payment'
            ],
            'order_created_at' => [
                'keys' => ['orderCreatedAt', 'createdAt'],
                'entity' => 'Payment',
                'isTimestamp' => true
            ],
            'order_completed_at' => [
                'keys' => ['orderCompleteAt', 'updatedAt'],
                'entity' => 'Payment',
                'isTimestamp' => true
            ],
            'refunded_amount' => [
                'keys' => ['refundedAmount'],
                'entity' => 'Payment'
            ],
            'provision_amount' => [
                'keys' => ['provisionAmount'],
                'entity' => 'Payment'
            ],
            'hash' => [
                'keys' => ['hash'],
                'entity' => 'Payment'
            ],
            'email' => [
                'keys' => ['email'],
                'entity' => 'Payment'
            ],
            'amount' => [
                'keys' => ['amount'],
                'entity' => 'Payment'
            ],
            'is_cash' => [
                'keys' => ['isCash'],
                'entity' => 'Payment'
            ],
            'send_push' => [
                'keys' => ['sendPush'],
                'entity' => 'Payment'
            ],
            'processing_time' => [
                'keys' => ['processingTime'],
                'entity' => 'Payment'
            ],
            'bin' => [
                'keys' => ['cardMetadata.bin'],
                'entity' => 'Card'
            ],
            'last_digits' => [
                'keys' => ['cardMetadata.lastDigits'],
                'entity' => 'Card'
            ],
            'payment_system' => [
                'keys' => ['paymentMethod', 'cardMetadata.paymentSystem'],
                'entity' => 'Card'
            ],
            'payment_group' => [
                'keys' => ['paymentMethodGroup'],
                'entity' => 'Card'
            ],
            'country' => [
                'keys' => ['cardMetadata.country'],
                'entity' => 'Card'
            ],
            'holder_name' => [
                'keys' => ['cardMetadata.holderName'],
                'entity' => 'Card'
            ],
        ];

        $data = [];
        foreach ($mapData as $field => $fieldData) {
            foreach ($fieldData['keys'] as $fieldName) {
                $fieldValue = $request[$fieldName];
                if (isset($fieldData['values']) && $fieldValue) {
                    foreach ($fieldData['values'] as $key => $values) {
                        if (in_array($fieldValue, $values)) {
                            $status = $key;
                            $data[$fieldData['entity']][$field] = $key;
                        }
                    }
                } else if (isset($fieldValue) || $fieldValue === '0') {
                    if (isset($fieldData['isTimestamp']) && $fieldData['isTimestamp']) {
                        if (is_numeric($fieldValue)) {
                            $fieldValue = Carbon::createFromTimestamp($fieldValue);
                        } else {
                            $fieldValue = Carbon::parse($fieldValue);
                        }
                    }

                    $data[$fieldData['entity']][$field] = $fieldValue;
                }
            }
        }

        $user = User::where('email', $data['Payment']['email'])->first();

        $card = Card::where('user_id', $user->id)->first();
        if (!$card) {
            $data['Card']['balance'] = $data['Payment']['amount'];
            $data['Card']['user_id'] = $user->id;
            $card = Card::create($data['Card']);
        } else {
            $card->update([
                'balance' => $card->balance + $data['Payment']['amount'],
            ]);
        }

        $data['Payment']['user_id'] = $user->id;
        Payment::create($data['Payment']);

        return response()->json($data);
    }
}