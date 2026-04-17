<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offre extends Model
{
    /** @use HasFactory<\Database\Factories\OffreFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'titre',
        'description',
        'localisation',
        'type',
        'actif'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function candidature(){
        return $this->hasMany(Candidature::class);
    }

}
