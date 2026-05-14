<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class FakeUsersSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()
            ->count(20)
            ->withoutTwoFactor()
            ->create();

        $this->command->info('20 fake users created.');
    }
}
