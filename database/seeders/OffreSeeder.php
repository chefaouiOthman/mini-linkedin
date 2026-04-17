<?php

namespace Database\Seeders;

use App\Models\Offre;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OffreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $recruteurs=User::where('role','recruteur')->get();
        foreach($recruteurs as $recruteur){
            Offre::factory(rand(2,3))->create([
                'user_id' => $recruteur->id
            ]);
        }
    }
}
