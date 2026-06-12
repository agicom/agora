<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@agora.test'],
            [
                'name' => 'Admin Agora',
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
                'password' => 'password',
            ],
        );
    }
}
