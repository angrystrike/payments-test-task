<?php

namespace App\Services;

use Carbon\Carbon;
use App\Services\PromoCodeService;
use App\Interfaces\PaymentServiceInterface;
use App\Models\Payment;
use App\Models\Order;
use App\Models\User;
use App\Models\Currency;


class PaymentService implements PaymentServiceInterface 
{
    private $currencyAmount = [
        'usdAmount', 'uahAmount', 'eurAmount'
    ];

    private $mapData;

    public function __construct() {
        $this->mapData = [
            'status' => [
                'keys' => ['state', 'status'],
                'values' => [
                    'completed' => ['complete', 'completed', 2],
                    'pending' => ['pending', 'processing', 4],
                    'refunded' => ['refunded'],
                    'failed' => ['failed', 3]
                ],
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
                'keys' => array_merge(['currency'], $this->currencyAmount),
                'entity' => 'Payment',
                'regex' => '/^([a-zA-Z]+)Amount$/'
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
                'keys' => array_merge(['amount'], $this->currencyAmount),
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
    }

    private function mapJsonToTableColumns($input)
    {
        $data = [];
        foreach ($this->mapData as $field => $fieldData) {
            foreach ($fieldData['keys'] as $fieldName) {
                $fieldValue = isset($input[$fieldName]) ? $input[$fieldName] : null;
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
                    } else if (isset($fieldData['regex'])) {
                        if (preg_match($fieldData['regex'], $fieldName, $matches)) {
                            $fieldValue = $matches[1];
                        }
                    }

                    $data[$fieldData['entity']][$field] = $fieldValue;
                }
            }
        }

        if (!isset($data['Payment']['email'])) {
            $order = Order::find($data['Payment']['order_id']);
            $data['Payment']['email'] = $order->user->email;
        }

        return $data;
    }

    public function store($data)
    {
        $transactionData = $this->mapJsonToTableColumns($data);
        $user = User::where('email', $transactionData['Payment']['email'])->first();

        $currency = Currency::where('title', $transactionData['Payment']['currency'])->first();
        $transactionData['Payment']['user_id'] = $user->id;
        $transactionData['Payment']['currency_id'] = $currency->id;
        Payment::create($transactionData['Payment']);

        return $transactionData;
    }
    
}