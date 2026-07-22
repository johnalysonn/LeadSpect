<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'osm_id' => 'node_' . $this->faker->unique()->numberBetween(100000, 999999),
            'name' => $this->faker->company(),
            'category' => $this->faker->randomElement(['Restaurante', 'Farmácia', 'Academia', 'Oficina', 'Loja']),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'neighborhood' => $this->faker->citySuffix(),
            'postal_code' => $this->faker->postcode(),
            'latitude' => $this->faker->latitude(-23.6, -23.4),
            'longitude' => $this->faker->longitude(-46.7, -46.5),
            'phone' => '119' . $this->faker->numberBetween(10000000, 99999999),
            'whatsapp' => '119' . $this->faker->numberBetween(10000000, 99999999),
            'email' => $this->faker->companyEmail(),
            'website' => 'https://' . $this->faker->domainName(),
            'status' => LeadStatus::A_PROSPECTAR,
            'is_favorite' => false,
        ];
    }
}
