<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's users.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Abdul Wahab',
            'username' => 'wahab',
            'password' => 'password',
        ]);

        User::factory()->create([
            'name' => 'Sajid',
            'username' => 'sajid',
            'password' => 'password',
        ]);
    }
}
