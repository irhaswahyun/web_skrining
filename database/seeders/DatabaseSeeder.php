<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Memastikan bahwa role dengan id 1 sudah ada
        Role::firstOrCreate(['id' => 1], ['Nama_Role' => 'Nakes', 'created_at' => now(), 'updated_at' => now()]);
        Role::firstOrCreate(['id' => 2], ['Nama_Role' => 'Kader', 'created_at' => now(), 'updated_at' => now()]);
        Role::firstOrCreate(['id' => 3], ['Nama_Role' => 'Admin', 'created_at' => now(), 'updated_at' => now()]);

        // Menambahkan user setelah role ada
        User::insert([
            [
                'nama' => 'Iyas',
            'email' => 'irhass@gmail.com',
            'id_role' => 1, // Menggunakan id yang sudah ada pada role
            'password' => bcrypt('12345678'),
            ],
            [
                'nama' => 'Admin',
                'email' => 'admin@gmail.com',
                'id_role' => 3, // Menggunakan id yang sudah ada pada role
                'password' => bcrypt('12345678'),
            ],
        ]);
    }
}
