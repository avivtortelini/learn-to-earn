<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'pemilik@kost.local'],
            [
                'name' => 'Pemilik Kost',
                'password' => Hash::make('password'),
                'role' => User::ROLE_OWNER,
            ]
        );

        User::updateOrCreate(
            ['email' => 'receptionist@kost.local'],
            [
                'name' => 'Receptionist Kost',
                'password' => Hash::make('password'),
                'role' => User::ROLE_RECEPTIONIST,
            ]
        );

        foreach (range(1, 10) as $number) {
            Room::firstOrCreate(
                ['number' => sprintf('A%02d', $number)],
                ['monthly_price' => 750000]
            );
        }
    }
}
