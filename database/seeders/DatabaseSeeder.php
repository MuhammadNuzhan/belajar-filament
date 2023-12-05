<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        User::factory()->create([
            'name' => 'Andi',
            'email' => 'andi@gmail.com',
            'password' => 'nuzhan123',
        ]);
        User::factory()->create([
            'name' => 'Nuzhan',
            'email' => 'n@gmail.com',
            'password' => 'nuzhan123',
        ]);
    }
}
