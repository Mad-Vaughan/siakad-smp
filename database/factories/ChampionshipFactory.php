<?php

namespace Database\Factories;

use App\Enums\ChampionLevel;
use App\Enums\ChampionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Championship>
 */
class ChampionshipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => \App\Models\Student::factory(),
            'name' => $this->faker->sentence(3),
            'year' => $this->faker->year(),
            'level' => $this->faker->randomElement([ChampionLevel::cases()]),
            'position' => $this->faker->randomElement(['juara 1', 'juara 2', 'juara 3', 'peserta']),
            'type' => $this->faker->randomElement([ChampionType::cases()]),
        ];
    }
}
