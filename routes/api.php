<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OffreController;
use Illuminate\Support\Facades\Route;

// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Routes protégées
Route::middleware('auth:api')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    //Profils
    Route::middleware('role:candidat')->group(function () {
        Route::post('/profil',                            [ProfilController::class, 'store']);
        Route::get('/profil',                             [ProfilController::class, 'show']);
        Route::put('/profil',                             [ProfilController::class, 'update']);
        Route::post('/profil/competences',                [ProfilController::class, 'addCompetence']);
        Route::delete('/profil/competences/{competence}', [ProfilController::class, 'removeCompetence']);
    });
    //Offre d’emploi
    
    Route::get('offres',        [OffreController::class, 'index']);
    Route::get('offres/{id}',   [OffreController::class, 'detail']);


//  Recruteur uniquement 
    Route::middleware('role:recruteur')->group(function () {
    Route::post('offres',             [OffreController::class, 'creation']);
    Route::put('offres/{id}',         [OffreController::class, 'update']);
    Route::delete('offres/{id}',      [OffreController::class, 'destroy']);
    });
    //Candidatures
    Route::middleware('role:candidat')->group(function () {
        Route::post('/offres/{offre}/candidater', [CandidatureController::class, 'postuler']);
        Route::get('/mes-candidatures',           [CandidatureController::class, 'mesCandidatures']);
    });

    Route::middleware('role:recruteur')->group(function () {
        Route::get('/offres/{offre}/candidatures',         [CandidatureController::class, 'candidaturesDuneOffre']);
        Route::patch('/candidatures/{candidature}/statut', [CandidatureController::class, 'changerStatut']);
    });

    //Administration
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/users',           [AdminController::class, 'listeUsers']);
        Route::delete('/users/{user}', [AdminController::class, 'supprimerUser']);
        Route::patch('/offres/{offre}',[AdminController::class, 'toggleOffre']);
    });
});