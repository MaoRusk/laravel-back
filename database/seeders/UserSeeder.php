<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'middle_name' => 'User',
                'last_name' => 'Dev',
                'email' => 'admin@dev.com',
                'email_verified_at' => now(),
                'user_name' => 'admin.dev',
                'password' => Hash::make('password'),
                'company_id' => 1,
                'role_id' => 1,
                'total_devices' => 1,
                'status' => 'Enabled'
            ],
            [
                'name' => 'User',
                'middle_name' => 'User',
                'last_name' => 'Dev',
                'email' => 'user@dev.com',
                'email_verified_at' => now(),
                'user_name' => 'user.dev',
                'password' => Hash::make('password'),
                'company_id' => 1,
                'role_id' => null,
                'total_devices' => 1,
                'status' => 'Enabled'
            ],
        ]);

        $user = User::where('user_name', 'admin.dev')->first();
        $user->assignRole(Roles::ADMIN);
    }
}
