<?php

namespace Database\Seeders;

use App\Models\DeliverySetting;
use App\Models\District;
use App\Models\Division;
use Illuminate\Database\Seeder;

class DeliveryLocationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Delivery Settings exist
        DeliverySetting::firstOrCreate(
            ['id' => 1],
            [
                'free_delivery_enabled' => true,
                'free_delivery_threshold' => 2000.00,
            ]
        );

        // 2. Divisions & Districts structure
        $divisionsData = [
            'Dhaka' => [
                'Dhaka', 'Faridpur', 'Gazipur', 'Gopalganj', 'Kishoreganj',
                'Madaripur', 'Manikganj', 'Munshiganj', 'Narayanganj',
                'Narsingdi', 'Rajbari', 'Shariatpur', 'Tangail',
            ],
            'Chattogram' => [
                'Chattogram', 'Cumilla', 'Bandarban', 'Brahmanbaria', 'Chandpur',
                "Cox's Bazar", 'Feni', 'Khagrachari', 'Lakshmipur', 'Noakhali',
                'Rangamati',
            ],
            'Barishal' => [
                'Barguna', 'Barishal', 'Bhola', 'Jhalokati', 'Patuakhali', 'Pirojpur',
            ],
            'Khulna' => [
                'Bagerhat', 'Chuadanga', 'Jashore', 'Jhenaidah', 'Khulna',
                'Kushtia', 'Magura', 'Meherpur', 'Narail', 'Satkhira',
            ],
            'Mymensingh' => [
                'Jamalpur', 'Mymensingh', 'Netrokona', 'Sherpur',
            ],
            'Rajshahi' => [
                'Bogura', 'Joypurhat', 'Naogaon', 'Natore', 'Chapainawabganj',
                'Pabna', 'Rajshahi', 'Sirajganj',
            ],
            'Rangpur' => [
                'Dinajpur', 'Gaibandha', 'Kurigram', 'Lalmonirhat', 'Nilphamari',
                'Panchagarh', 'Rangpur', 'Thakurgaon',
            ],
            'Sylhet' => [
                'Habiganj', 'Maulvibazar', 'Sunamganj', 'Sylhet',
            ],
        ];

        $specialRateDistricts = [
            'Dhaka' => 80.00,
            'Cumilla' => 80.00,
        ];
        $defaultRate = 130.00;

        $divSort = 10;
        foreach ($divisionsData as $divisionName => $districts) {
            $division = Division::firstOrCreate(
                ['name' => $divisionName],
                ['status' => true, 'sort_order' => $divSort]
            );
            $divSort += 10;

            $distSort = 10;
            foreach ($districts as $districtName) {
                $charge = $specialRateDistricts[$districtName] ?? $defaultRate;

                District::firstOrCreate(
                    [
                        'division_id' => $division->id,
                        'name' => $districtName,
                    ],
                    [
                        'delivery_charge' => $charge,
                        'status' => true,
                        'sort_order' => $distSort,
                    ]
                );
                $distSort += 10;
            }
        }
    }
}
