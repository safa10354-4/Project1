<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locations = [
            // سوريا
            ['country' => 'Syria', 'region' => 'Damascus', 'province' => 'Damascus', 'latitude' => 33.5138, 'longitude' => 36.2765],
            ['country' => 'Syria', 'region' => 'Aleppo', 'province' => 'Aleppo', 'latitude' => 36.2021, 'longitude' => 37.1343],
            ['country' => 'Syria', 'region' => 'Homs', 'province' => 'Homs', 'latitude' => 34.7269, 'longitude' => 36.7234],
            ['country' => 'Syria', 'region' => 'Latakia', 'province' => 'Latakia', 'latitude' => 35.5300, 'longitude' => 35.7900],
            ['country' => 'Syria', 'region' => 'Deir ez-Zor', 'province' => 'Deir ez-Zor', 'latitude' => 35.3342, 'longitude' => 40.1387],

            // لبنان
            ['country' => 'Lebanon', 'region' => 'Beirut', 'province' => 'Beirut', 'latitude' => 33.8938, 'longitude' => 35.5018],
            ['country' => 'Lebanon', 'region' => 'Tripoli', 'province' => 'North Lebanon', 'latitude' => 34.4367, 'longitude' => 35.8497],
            ['country' => 'Lebanon', 'region' => 'Sidon', 'province' => 'South Lebanon', 'latitude' => 33.5582, 'longitude' => 35.3756],
            ['country' => 'Lebanon', 'region' => 'Baalbek', 'province' => 'Bekaa', 'latitude' => 34.0060, 'longitude' => 36.2110],
            ['country' => 'Lebanon', 'region' => 'Zahle', 'province' => 'Bekaa', 'latitude' => 33.8486, 'longitude' => 35.9042],
        ];

        DB::table('locations')->insert($locations);
    }
}
