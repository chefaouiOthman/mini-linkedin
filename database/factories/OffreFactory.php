<?php

namespace Database\Factories;

use App\Models\Offre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Offre>
 */
class OffreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'titre'=>fake()->jobTitle(),
            'description'=>fake()->paragraph(3,true),
            'localisation'=>fake()->city(),
            'type'=>fake()->randomElement(['CDI', 'CDD', 'Stage']),
            'actif'=>fake()->boolean(90), 
        ];
    }
}
