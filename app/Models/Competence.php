<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competence extends Model
{
    /** @use HasFactory<\Database\Factories\CompetenceFactory> */
    use HasFactory;
    protected $fillable = [
        'nom',
        'categorie',
    ];
    public function profil(){
        return $this->belongsToMany(Profil::class,'profil_competence')->withPivot('niveau');
    }
}
