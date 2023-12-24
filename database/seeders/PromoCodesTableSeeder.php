<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;
use Illuminate\Support\Facades\DB;


class PromoCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usd = Currency::where('title', 'USD')->first();
        $uah = Currency::where('title', 'UAH')->first();
        $eur = Currency::where('title', 'EUR')->first();

        $promoCodes = [
            [
                'code' => 'qwerty',
                'currency_id' => $usd->id,
                'bonus_amount' => 20.05,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'hello',
                'currency_id' => $usd->id,
                'bonus_amount' => 120.05,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'test',
                'currency_id' => $uah->id,
                'bonus_amount' => 20.05,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'present',
                'currency_id' => $eur->id,
                'bonus_amount' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('promo_codes')->insert($promoCodes);
    }
}
