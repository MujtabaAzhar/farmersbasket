<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourierService;

class CourierServiceSeeder extends Seeder
{
    public function run(): void
    {
        $couriers = [
            [
                'name'                   => "Farmer's Basket Delivery",
                'code'                   => 'internal',
                'is_active'              => true,
                'api_base_url'           => null,
                'tracking_url_template'  => null,
            ],
            [
                'name'                   => 'Leopards Courier',
                'code'                   => 'leopards',
                'is_active'              => true,
                'api_base_url'           => 'https://merchantapi.leopardscourier.com',
                'tracking_url_template'  => 'https://leopardscourier.com/track-your-packet/?tracking_number={tracking_number}',
            ],
            [
                'name'                   => 'TCS Couriers',
                'code'                   => 'tcs',
                'is_active'              => true,
                'api_base_url'           => 'https://api.tcscourier.com/production/v1',
                'tracking_url_template'  => 'https://www.tcscourier.com/track?consignment={tracking_number}',
            ],
            [
                'name'                   => 'M&P Express',
                'code'                   => 'mnp',
                'is_active'              => true,
                'api_base_url'           => 'https://api.mpexpress.com.pk',
                'tracking_url_template'  => 'https://mp.com.pk/tracking?tracking_number={tracking_number}',
            ],
        ];

        foreach ($couriers as $data) {
            CourierService::updateOrCreate(['code' => $data['code']], $data);
        }
    }
}
