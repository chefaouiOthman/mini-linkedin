<?php

namespace Database\Factories;

use App\Models\Profil;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profil>
 */
class ProfilFactory extends Factory
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
            'titre' =>fake()->jobTitle(),
            'bio' =>fake()->paragraph(),
            'localisation' =>fake()->city(),
            'disponible' =>fake()->boolean(80),
        ];
    }
}
