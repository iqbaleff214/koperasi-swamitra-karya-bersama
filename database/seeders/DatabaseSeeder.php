<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $users = [
            [
                'name' => 'Manajer',
                'email' => 'manajer@swamitra.com',
                'password' => Hash::make('admin'),
                'role' => 'manager',
                'phone' => '0821',
                'joined_at' => Carbon::now(),
            ],
            [
                'name' => 'Teller',
                'email' => 'teller@swamitra.com',
                'password' => Hash::make('admin'),
                'role' => 'teller',
                'phone' => '0822',
                'joined_at' => Carbon::now(),
            ],
            [
                'name' => 'Collector',
                'email' => 'kolektor@swamitra.com',
                'password' => Hash::make('admin'),
                'role' => 'collector',
                'phone' => '0823',
                'joined_at' => Carbon::now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
