<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PharmacySeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
<<<<<<< HEAD
=======
            PaymentSeeder::class,
>>>>>>> 29d7a803bcadfd898c5fffc35350b9d60f2a165a
        ]);
    }
}
