<?php

namespace App\Interfaces;

use App\Models\Payment;


interface PaymentServiceInterface
{
    public function store($data);
}