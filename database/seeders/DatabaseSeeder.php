<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'id' => 1,
                'title' => '一般',
                'bg' => 'primary'
            ],
            [
                'id' => 2,
                'title' => '管理者',
                'bg' => 'info'
            ],
            [
                'id' => 3,
                'title' => '利用停止',
                'bg' => 'secondary'
            ]
        ]);

        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => 2,
            'number_of_lines' => 0,
        ]);

        $user->survey()->create([
            'title' => 'vavdavda',
            'note' => 'nobe',
            'voice_name' => 'ja-JP-Standard-A'
        ]);

    }
}
