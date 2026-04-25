<?php

namespace Database\Factories;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'nisn' => $this->faker->unique()->numerify('##########'),
            'date_of_birth' => $this->faker->date(),
            'gender' => $this->faker->randomElement(Gender::cases()),
            'email_verified_at' => now(),
            'remember_token' => \Illuminate\Support\Str::random(10),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (\App\Models\Student $student) {
            $student->assignRole(\App\Enums\Roles::STUDENT->value);
        });
    }
}
