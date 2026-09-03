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
            'nik' => $this->faker->unique()->numerify('3217##########'), 
            'username' => $this->faker->userName(),
            'nik_verified_at' => now(),
            'role' => 'Warga', 
            'password' => static::$password ??= Hash::make('password'),
            'status_akun' => 'Active',
            'last_login' => null,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's NIK should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'nik_verified_at' => null,
            'status_akun' => 'Unverified',
        ]);
    }
}