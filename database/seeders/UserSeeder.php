<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                'nik' => null,
                'name' => 'Admin 1',
                'address' => 'Example Address No 1',
                'phone' => '628xxxx',
                'email' => 'admin@simpleapi.com',
                'password' => Crypt::encryptString('admin123'),
                'role' => 'admin',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'nik' => null,
                'name' => 'Librarian 1',
                'address' => 'Example Address No 2',
                'phone' => '628xxxx',
                'email' => 'librarian@simpleapi.com',
                'password' => Crypt::encryptString('librarian123'),
                'role' => 'librarian',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'nik' => null,
                'name' => 'Member 1',
                'address' => 'Example Address No 3',
                'phone' => '628xxxx',
                'email' => 'member@simpleapi.com',
                'password' => Crypt::encryptString('member123'),
                'role' => 'member',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
