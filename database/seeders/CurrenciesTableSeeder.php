<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CurrenciesTableSeeder extends Seeder
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
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'UAH',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'EUR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('currencies')->insert($currencies);
    }
}
