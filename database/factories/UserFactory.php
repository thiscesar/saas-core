<?php

declare(strict_types = 1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
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
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'remember_token'    => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user was created via Slack OAuth.
     */
    public function withSlackAuth(): static
    {
        return $this->state(fn (array $attributes): array => [
            'slack_id'            => 'U' . fake()->unique()->numerify('########'),
            'slack_access_token'  => 'xoxp-' . Str::random(40),
            'slack_refresh_token' => 'xoxr-' . Str::random(40),
            'avatar_url'          => fake()->imageUrl(),
            'password'            => null,
        ]);
    }
}
