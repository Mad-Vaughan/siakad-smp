<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentAssesment>
 */
class StudentAssesmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => \App\Models\User::factory()->isStudent(),
            'assesment_id' => \App\Models\Assesment::factory(),
            'score' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}
