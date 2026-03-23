<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SourceTownSeeder::class,
            DestinationTownSeeder::class,
        ]);

        User::query()->updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'phone' => '09900000000',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'account_code' => 'ACC00001',
                'is_active' => true,
            ]
        );
    }
}
