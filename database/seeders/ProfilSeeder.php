<?php

namespace Database\Seeders;

use App\Models\Competence;
use App\Models\Profil;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $competences=Competence::all();
        User::factory(10)->create(['role' => 'candidat'])->each(function($candidat) use ($competences){
            $profil=Profil::factory()->create([
                'user_id' => $candidat->id
            ]);
            $randomComp=$competences->random(rand(3,5));
            foreach($randomComp as $competence){
                $profil->competences()->attach($competence->id,[
                    'niveau' => fake()->randomElement(['debutant', 'intermediaire', 'expert'])
                ]);
            }
        });
    }
}
