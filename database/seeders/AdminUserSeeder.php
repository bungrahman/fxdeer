<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@fxdeer.com'],
            [
                'password' => Hash::make('passwordadmin123'),
                'role' => 'ADMIN',
                'status' => 'ACTIVE',
                'timezone' => 'Asia/Jakarta',
                'default_language' => 'id',
            ]
        );
    }
}
