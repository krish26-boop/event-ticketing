<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Generates a new user if not provided
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'location' => $this->faker->city(),
            'tickets_available' => $this->faker->numberBetween(10, 200),
        ];
    }
}
