<?php

namespace Database\Factories;

use App\Enums\AssesmentType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assessment>
 */
class AssesmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'classroom_id' => \App\Models\Classroom::factory(),
            'subject_id' => \App\Models\Subject::factory(),
            'type' => $this->faker->randomElement(AssesmentType::cases()),
        ];
    }
}
