<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::insert([
            [
                'no_hp' => '000',
                'nama' => 'Admin',
                'pin' => '$2a$12$MhPt9ulTF0LpERO3/faRKuAe3WYFnpz.kCjP4yOebbSUkZ6Jy/91C', // 0000
                'tipe' => 'A'
            ],
            [
                'no_hp' => '123456',
                'nama' => 'Cashier 1',
                'pin' => '$2a$12$wO524Q6yewzSWHdEbk1oA.L.9pbvfEHxNxRFJz4LtgnV03mge4Xj2', // 2233
                'tipe' => 'C'
            ],
            [
                'no_hp' => '654321',
                'nama' => 'Cashier 2',
                'pin' => '$2a$12$wO524Q6yewzSWHdEbk1oA.L.9pbvfEHxNxRFJz4LtgnV03mge4Xj2', // 2233
                'tipe' => 'C'
            ],
        ]);
    }
}
