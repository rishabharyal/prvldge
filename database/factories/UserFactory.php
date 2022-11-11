<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $gender = $this->faker->randomElement(['m', 'f', 'o']);
        $name = $this->faker->name;

        return [
            'name' => $name,
            'email' => $this->faker->email,
            'username' => uniqid('user_', true),
            'password' => app('hash')->make('12345678'),
            'country' => 'NP',
            'phone_number' => $this->faker->phoneNumber,
            'birthday' => $this->faker->date,
            'gender' => $gender,
            'is_phone_verified' => rand(0, 1)
        ];
    }
}
