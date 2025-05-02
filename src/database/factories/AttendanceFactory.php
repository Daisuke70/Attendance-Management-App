<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'date' => now()->toDateString(),
            'clock_in' => '08:30:00',
            'clock_out' => '17:30:00',
            'status' => 'finished',
            'note' => null,
        ];
    }
}
