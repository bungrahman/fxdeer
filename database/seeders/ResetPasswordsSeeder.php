<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update SEMUA user kecuali admin utamanya
        $users = User::where('email', '!=', 'admin@newsauto.com')->get();
        foreach ($users as $user) {
            $user->update([
                'password' => Hash::make('passworduser123')
            ]);
        }
    }
}
