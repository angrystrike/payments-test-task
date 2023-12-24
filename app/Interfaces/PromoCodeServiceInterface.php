<?php

namespace App\Interfaces;

use App\Models\PromoCode;


interface PromoCodeServiceInterface
{
    public function store($code, $email);
}