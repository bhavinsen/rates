<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class RatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'valute_id' => 'R01270',
            'num_code' => '356',
            'char_code' => 'INR',
            'nominal' => '10',
            'name' => 'Индийских рупий',
            'value' => 10.0730,
            'valcurs_date' => date('Y-m-d')
        ]);

        DB::table('users')->insert([
            'valute_id' => 'R01020A',
            'num_code' => '944',
            'char_code' => 'AZN',
            'nominal' => '1',
            'name' => 'Азербайджанский манат',
            'value' => 43.7039,
            'valcurs_date' => date('Y-m-d')
        ]);
    }
}
