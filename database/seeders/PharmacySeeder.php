<?php

namespace Database\Seeders;

use App\Models\Pharmacy;
use Illuminate\Database\Seeder;

class PharmacySeeder extends Seeder
{
    public function run(): void
    {
        $pharmacies = [
            ['pharmacy_name' => 'Al Noor Pharmacy', 'owner_name' => 'Maha Salem', 'phone' => '555-1001', 'area' => 'Downtown', 'address' => '12 Main Street', 'notes' => 'Prefers morning visits.'],
            ['pharmacy_name' => 'Green Leaf Pharmacy', 'owner_name' => 'Ibrahim Adel', 'phone' => '555-1002', 'area' => 'West Side', 'address' => '44 Olive Ave'],
            ['pharmacy_name' => 'CityCare Pharmacy', 'owner_name' => 'Rana Ali', 'phone' => '555-1003', 'area' => 'Market District', 'address' => '9 Market Road'],
            ['pharmacy_name' => 'Sunrise Drugstore', 'owner_name' => 'Khaled Omar', 'phone' => '555-1004', 'area' => 'North Heights', 'address' => '221 Hill Street'],
            ['pharmacy_name' => 'MediPoint Pharmacy', 'owner_name' => 'Nour Mostafa', 'phone' => '555-1005', 'area' => 'Riverside', 'address' => '78 River Lane', 'notes' => 'Interested in skin-care bundles.'],
        ];

        foreach ($pharmacies as $pharmacy) {
            Pharmacy::create($pharmacy);
        }
    }
}
