<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ShortUrlFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Company::factory(),
            'user_id' => \App\Models\User::factory(),
            'original_url' => fake()->url(),
            'short_code' => fake()->regexify('[A-Za-z0-9]{10}'),
        ];
    }
}
