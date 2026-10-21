<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'seller@stoklestari.test'],
            [
                'name' => 'Toko Lestari',
                'password' => 'password',
            ],
        );
    }
}
