<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\AdminController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([],function(){
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
    Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:api');
    Route::get('/me',[AuthController::class,'me'])->middleware(['auth:api']);
});

Route::middleware(['auth:api', 'role:candidat'])->group(function () {
    Route::post('/offres/{offre}/candidater', [CandidatureController::class, 'postuler']);
    Route::get('/mes-candidatures', [CandidatureController::class, 'mesCandidatures']);
});

Route::middleware(['auth:api', 'role:recruteur'])->group(function () {
    Route::get('/offres/{offre}/candidatures', [CandidatureController::class, 'candidaturesDuneOffre']);
    Route::patch('/candidatures/{candidature}/statut', [CandidatureController::class, 'changerStatut']);
});

Route::middleware(['auth:api', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/users', [AdminController::class, 'listeUsers']);
    Route::delete('/users/{user}', [AdminController::class, 'supprimerUser']);
    Route::patch('/offres/{offre}', [AdminController::class, 'toggleOffre']);
});
