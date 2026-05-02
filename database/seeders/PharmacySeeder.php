<?php

namespace Database\Seeders;

use App\Models\Pharmacy;
use Illuminate\Database\Seeder;

class PharmacySeeder extends Seeder
{
    public function run(): void
    {
        $pharmacies = [
            ['pharmacy_name' => 'Al Noor Pharmacy', 'owner_name' => 'Ahmed Salem', 'phone' => '+20-100-100-1001', 'area' => 'Nasr City', 'address' => 'Street 12, Building 4', 'notes' => 'Interested in skincare line'],
            ['pharmacy_name' => 'Green Cross Pharmacy', 'owner_name' => 'Mona Adel', 'phone' => '+20-100-100-1002', 'area' => 'Heliopolis', 'address' => 'El Merghany Road', 'notes' => 'Prefers weekend visits'],
            ['pharmacy_name' => 'CarePlus Pharmacy', 'owner_name' => 'Youssef Nabil', 'phone' => '+20-100-100-1003', 'area' => 'Maadi', 'address' => 'Road 9', 'notes' => null],
            ['pharmacy_name' => 'Family Health Pharmacy', 'owner_name' => 'Sara Fathy', 'phone' => '+20-100-100-1004', 'area' => 'Dokki', 'address' => 'Tahrir Street', 'notes' => 'Asks for price updates monthly'],
            ['pharmacy_name' => 'Sunrise Pharmacy', 'owner_name' => 'Khaled Mostafa', 'phone' => '+20-100-100-1005', 'area' => 'Mohandessin', 'address' => 'Gameat El Dowal', 'notes' => 'Potential for expansion'],
        ];

        foreach ($pharmacies as $pharmacy) {
            Pharmacy::create($pharmacy);
        }
    }
}
