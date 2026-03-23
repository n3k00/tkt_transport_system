<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => '09'.fake()->unique()->numerify('#########'),
            'email' => fake()->boolean(70) ? fake()->unique()->safeEmail() : null,
            'password' => static::$password ??= Hash::make('password'),
            'role' => fake()->randomElement(['admin', 'staff']),
            'account_code' => strtoupper(fake()->unique()->bothify('ACC#####')),
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }
}
