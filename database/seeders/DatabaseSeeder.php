<?php

namespace Database\Seeders;

use App\Models\Topup;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdraw;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class
        ]);

        User::factory(20)->create();
        Topup::factory(50)->create();
        Transaction::factory(100)->create();
        Withdraw::factory(30)->create();
    }
}
