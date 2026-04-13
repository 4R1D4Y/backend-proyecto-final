<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. EL ADMINISTRADOR
        User::create([
            'email' => 'admin@creo.com',
            'password' => Hash::make('admin1234'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // 2. USUARIO NORMAL ACTIVO
        User::create([
            'email' => 'user@creo.com',
            'password' => Hash::make('user1234'),
            'role' => 'user',
            'status' => 'active',
        ]);
    }
}
