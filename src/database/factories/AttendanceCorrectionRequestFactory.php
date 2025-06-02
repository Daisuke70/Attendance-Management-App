<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Attendance;

class AttendanceCorrectionRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
        // database/factories/AttendanceCorrectionRequestFactory.php

public function definition()
{
    return [
        'attendance_id' => Attendance::factory(),
        'user_id' => User::factory(),
        'new_note' => $this->faker->sentence,
        'status' => 'pending',
    ];
}
}
