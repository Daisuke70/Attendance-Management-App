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
                'name' => '山田太郎',
                'email' => 'test@user.com',
                'password' => Hash::make('11111111'),
                'role' => 'user',
            ]);
        }

        if (!User::where('email', 'test@user2.com')->exists()) {
            User::factory()->create([
                'name' => '西怜奈',
                'email' => 'test@user2.com',
                'password' => Hash::make('22222222'),
                'role' => 'user',
            ]);
        }

        if (!User::where('email', 'test@user3.com')->exists()) {
            User::factory()->create([
                'name' => '増田一世',
                'email' => 'test@user3.com',
                'password' => Hash::make('33333333'),
                'role' => 'user',
            ]);
        }

        if (!User::where('email', 'test@user4.com')->exists()) {
            User::factory()->create([
                'name' => '山本敬吉',
                'email' => 'test@user4.com',
                'password' => Hash::make('44444444'),
                'role' => 'user',
            ]);
        }

        if (!User::where('email', 'test@user5.com')->exists()) {
            User::factory()->create([
                'name' => '秋田朋美',
                'email' => 'test@user5.com',
                'password' => Hash::make('55555555'),
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
