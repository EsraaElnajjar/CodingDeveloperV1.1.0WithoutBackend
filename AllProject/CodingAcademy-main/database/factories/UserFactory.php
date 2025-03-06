<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'role' => fake()->randomElement([0, 1]), // 0 = مستخدم، 1 = مشرف
            'reservations' => fake()->randomElement([0, 1]), // 0 = غير محجوز، 1 = محجوز
            'user_add_id' => fake()->uuid(),
            'image' => fake()->imageUrl(), // صورة عشوائية
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'), // كلمة مرور مشفرة
        ];
    }
    

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return $this
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
