<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BreakTimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'attendance_id' => 1,
            'start_time' => '12:00:00',
            'end_time' => '12:30:00',
        ];
    }
}
