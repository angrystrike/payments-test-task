<?php

namespace App\Services;

use Carbon\Carbon;
use App\Services\PromoCodeService;
use App\Interfaces\PaymentServiceInterface;
use App\Models\Payment;
use App\Models\Order;
use App\Models\User;
use App\Models\Currency;


/**
 * Class PaymentService
 *
 * A service class for handling payment-related operations.
 */
class PaymentService implements PaymentServiceInterface 
{
    private $currencyAmount = [
        'usdAmount', 'uahAmount', 'eurAmount'
    ];

    private $mapData;

    public function __construct() {
        /*
         * Define mapping data for JSON to table column transformation.
         * We can get same data, but with different keys. For example status can be named state or type in future.
         * $this->mapData = [
         *     'column_name_in_db' => [
         *         'keys' => ['possible', 'keys', 'names', 'in', 'json'],
         *         'values' => [
         *             'enum_name_in_db' => ['possible', 'status', 'name', 'in', 'json']
         *         ],
         *         'entity' => 'entity_for_easier_mapping' 
         *     ]
         * ]
         */
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

    /**
     * Maps JSON input data to corresponding table columns.
     *
     * @param array $input The input data in JSON format.
     *
     * @return array The mapped data.
     */
    private function mapJsonToTableColumns($input)
    {
        $data = [];
        foreach ($this->mapData as $field => $fieldData) {
            foreach ($fieldData['keys'] as $fieldName) {
                $fieldValue = isset($input[$fieldName]) ? $input[$fieldName] : null;

                if (isset($fieldData['values']) && $fieldValue) {
                    $data[$fieldData['entity']][$field] = null;
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

        // If email is not present in JSON, find by order_id
        if (!isset($data['Payment']['email'])) {
            $order = Order::find($data['Payment']['order_id']);
            $data['Payment']['email'] = $order->user->email;
        }

        return $data;
    }

    public function store($data)
    {
        $transactionData = $this->mapJsonToTableColumns($data);

        if (
            !isset($transactionData['Payment']['transaction_id']) ||
            !isset($transactionData['Payment']['order_id']) ||
            !isset($transactionData['Payment']['amount']) ||
            !isset($transactionData['Payment']['currency']) ||
            !isset($transactionData['Payment']['status'])
        ) {
            throw new \InvalidArgumentException(
                'transaction_id, order_id, amount, currency, status fields are required'
            );
        }

        $user = User::where('email', $transactionData['Payment']['email'])->first();
        if (!$user) {
            throw new \InvalidArgumentException('Could not find user.');
        }

        $currency = Currency::where('title', $transactionData['Payment']['currency'])->first();
        if (!$currency) {
            throw new \InvalidArgumentException('This currency is unsupported.');
        }

        $transactionData['Payment']['user_id'] = $user->id;
        $transactionData['Payment']['currency_id'] = $currency->id;
        Payment::create($transactionData['Payment']);

        return $transactionData;
    }
    
}