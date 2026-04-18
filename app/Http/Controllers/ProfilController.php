<?php

namespace App\Http\Controllers;

use App\Models\Profil;
use App\Models\Competence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{

    /**POST /api/profil : Créer son profil (une seule fois)*/
    public function store(Request $request)
    {
        $user = Auth::user();

        // 1. Vérification de l'unicité : Un profil maximum par utilisateur
        if ($user->profil) {
            return response()->json(['error' => 'Vous avez déjà un profil.'], 400);
        }
        // 2. Validation des données
        $validated = $request->validate([
            'titre'        => 'required|string|max:150',
            'bio'          => 'required|string|min:10',
            'localisation' => 'required|string',
        ]);
        // 3. Création
        $profil = $user->profil()->create(array_merge($validated, [
            'disponible' => true
        ]));
        return response()->json(['message' => 'Profil créé', 'data' => $profil], 201);
    }
    /** GET /api/profil : Consulter son propre profil */
    public function show()
    {
        $profil = Auth::user()->profil()->with('competences')->first();
        if (!$profil) {
            return response()->json(['error' => 'Profil non trouvé'], 404);
        }
        return response()->json($profil);
    }
        /** PUT /api/profil : Modifier son profil */
        public function update(Request $request)
        {
            $profil=Auth::user()->profil;
            if (!$profil) {
                return response()->json(['error'=>'Profil non trouvé'],404);
            }
            $validated = $request->validate([
                'titre'        => 'sometimes|string|max:150',
                'bio'          => 'sometimes|string|min:10',
                'localisation' => 'sometimes|string',
                'disponible'   => 'sometimes|boolean',
            ]);
            $profil->update($validated);
            return response()->json([
                'message' => 'Profil mis à jour',
                'data'    => $profil->load('competences')
            ]);
        }
    /** POST /api/profil/competences : Ajouter une compétence avec niveau */
    public function addCompetence(Request $request)
    {
        $profil=Auth::user()->profil;
        if (!$profil) {
            return response()->json(['error' => 'Veuillez d\'abord créer un profil'], 404);
        }
        $validated = $request->validate([
            'competence_id' => 'required|exists:competences,id',
            'niveau'        => 'required|in:débutant,intermédiaire,expert',
        ]);
        // syncWithoutDetaching ajoute la compétence sans supprimer les anciennes et evite les doublons
        $profil->competences()->syncWithoutDetaching([
            $validated['competence_id'] => ['niveau' => $validated['niveau']]
        ]);
        return response()->json([
            'message' => 'Compétence ajoutée au profil',
            'data'    => $profil->competences()->where('competences.id', $validated['competence_id'])->first()
        ]);
    }
    /** DELETE /api/profil/competences/{competence} : Retirer une compétence */
    public function removeCompetence($competence)
    {
        $profil = Auth::user()->profil;
        if (!$profil) {
            return response()->json(['error'=>'Profil non trouvé'],404);
        }
        //Vérifier que la compétence est bien attachée au profil
        //.exists() est plus performant qu'un .first() ou .count() car il génère un SELECT EXISTS(...) en SQL
        //une seule vérification booléenne sans charger les données.
        $exists = $profil->competences()->where('competences.id', $competence)->exists();
        if (!$exists) {
            return response()->json(['error' => 'Compétence non trouvée dans votre profil'], 404);
        }
        $profil->competences()->detach($competence);
        return response()->json(['message' => 'Compétence retirée']);
    }
}
