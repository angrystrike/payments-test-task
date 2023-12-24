<?php

namespace App\Interfaces;

use App\Models\Payment;


interface CardServiceInterface
{
    public function store($data, $email, $amount);
}