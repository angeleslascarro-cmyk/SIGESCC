<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sigescc.test'],
            ['name' => 'Admin', 'password' => Hash::make('admin123'), 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'agent@sigescc.test'],
            ['name' => 'Agente', 'password' => Hash::make('agent123'), 'role' => 'agent']
        );

      
        User::updateOrCreate(
            ['email' => 'dubraska@sigescc.test'],
            ['name' => 'Dubraska', 'password' => Hash::make('dubraska123'), 'role' => 'admin']
        );
    }
}
