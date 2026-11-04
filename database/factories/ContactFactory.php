<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;


    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'gender' => $this->faker->randomElement([1, 2, 3]),
            'email' => $this->faker->unique()->safeEmail(),
            'tel1' => $this->faker->regexify('[0-9]{3}'),
            'tel2' => $this->faker->regexify('[0-9]{4}'),
            'tel3' => $this->faker->regexify('[0-9]{4}'),
            'address' => $this->faker->streetAddress(),
            'building' => $this->faker->secondaryAddress(),
            'category_id' => Category::factory(),
            'body' => $this->faker->realText(100),
        ];
    }
}
