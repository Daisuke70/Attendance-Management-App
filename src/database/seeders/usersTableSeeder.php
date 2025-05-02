<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class usersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!User::where('email', 'test@user.com')->exists()) {
            User::factory()->create([
                'name' => 'ユーザー太郎',
                'email' => 'test@user.com',
                'password' => Hash::make('12345678'),
                'role' => 'user',
            ]);
        }
    
        if (!User::where('email', 'test@admin.com')->exists()) {
            User::factory()->create([
                'name' => '管理太郎',
                'email' => 'test@admin.com',
                'password' => Hash::make('87654321'),
                'role' => 'admin',
            ]);
        }
    }
}
