<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = [
            [
                'title' => 'USD',
                'rateToUsd' => 1.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'UAH',
                'rateToUsd' => 32.0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'EUR',
                'rateToUsd' => 0.9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('currencies')->insert($currencies);
    }
}
