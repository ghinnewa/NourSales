<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Hydrating Face Cream', 'brand' => 'DermaGlow', 'price' => 18.50, 'description' => 'Daily hydration cream for normal to dry skin.'],
            ['name' => 'SPF 50 Sunscreen', 'brand' => 'SunShield', 'price' => 14.99, 'description' => 'Broad-spectrum UV protection, lightweight formula.'],
            ['name' => 'Gentle Foam Cleanser', 'brand' => 'PureSkin', 'price' => 11.25, 'description' => 'Soap-free cleanser that removes impurities without dryness.'],
            ['name' => 'Vitamin C Brightening Serum', 'brand' => 'LumiCare', 'price' => 22.00, 'description' => 'Antioxidant serum to improve tone and radiance.'],
            ['name' => 'Daily Moisturiser', 'brand' => 'SoftLeaf', 'price' => 13.75, 'description' => 'Fast-absorbing moisturiser for everyday use.'],
            ['name' => 'Nourishing Shampoo', 'brand' => 'HairBloom', 'price' => 9.80, 'description' => 'Cleanses and nourishes dry or damaged hair.'],
            ['name' => 'Silky Conditioner', 'brand' => 'HairBloom', 'price' => 10.20, 'description' => 'Smooths and detangles for softer hair.'],
            ['name' => 'Shea Body Lotion', 'brand' => 'VelvetTouch', 'price' => 12.40, 'description' => 'Rich body lotion with long-lasting moisture.'],
            ['name' => 'Repair Lip Balm', 'brand' => 'LipCare+', 'price' => 4.95, 'description' => 'Pocket-size balm that soothes and protects lips.'],
            ['name' => 'Clay Face Mask', 'brand' => 'ClearTone', 'price' => 15.60, 'description' => 'Deep-cleansing mask to reduce excess oil.'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
