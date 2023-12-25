<?php

namespace App\Helpers;
 
use App\Models\Currency;


class CurrencyConverter 
{
    /**
     * Convert currency from one type to another.
     *
     * @param float  $inputValue      The input value to convert.
     * @param string $inputCurrency   The input currency code.
     * @param string $outputCurrency  The output currency code (default is 'usd').
     *
     * @return float  The converted currency value.
     * @throws \InvalidArgumentException  If unsupported currencies are provided.
     */
    public function convert($inputValue, $inputCurrency, $outputCurrency = 'usd')
    {
        $inputCurrency = Currency::where('title', $inputCurrency)->first();
        $outputCurrency = Currency::where('title', $outputCurrency)->first();

        if (!$inputCurrency || !$outputCurrency) {
            throw new \InvalidArgumentException('Unsupported currency');
        }

        // Convert the input value to USD using the rateToUsd of the input currency.
        $usdValue = $inputValue / $inputCurrency->rateToUsd;
        
        // Convert the USD value to the output currency using the rateToUsd of the output currency.
        $output = $usdValue * $outputCurrency->rateToUsd;

        return $output;
    }
}