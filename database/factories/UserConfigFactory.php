<?php

namespace Database\Factories;

use App\Models\UserConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserConfigFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserConfig::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'telegram_user_id' => $this->faker->unique()->randomNumber(9),
            'username' => $this->faker->unique()->userName(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'language' => $this->faker->randomElement(['en', 'es']),
            'notifications_enabled' => $this->faker->boolean(),
        ];
    }
}

