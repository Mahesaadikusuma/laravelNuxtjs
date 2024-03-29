<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'gender' => $this->faker->randomElement(['MALE', 'FEMALE']),
            'age' => $this->faker->numberBetween(17, 45),
            'phone' => $this->faker->phoneNumber(),
            'team_id' => $this->faker->numberBetween(1, 5),
            'role_id' => $this->faker->numberBetween(1, 51),
            

        ];
    }
}
