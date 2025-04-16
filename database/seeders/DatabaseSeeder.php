<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Role::insert([
            [
                'Nama_Role' => 'Nakes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Nama_Role' => 'Kader',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        User::create([
            'name' => 'Irhas Wahyu Ningtyas',
            'email' => 'irhas@gmail.com',
            'id_role' => '1',
            'password' => bcrypt('12345678'),
        ]);
    }
}
