<?php

namespace App\Http\Controllers;

use App\Events\CandidatureDeposee;
use App\Events\StatutCandidatureMis;
use App\Models\Candidature;
use App\Models\Offre;
use Illuminate\Http\Request;

class CandidatureController extends Controller
{

    public function postuler(Request $request, Offre $offre)
    {

        if (!$offre->actif) {
            return response()->json(['message' => 'Cette offre n\'est plus active.'], 422);
        }

        $profil = $request->user()->profil;

        if (!$profil) {
            return response()->json(['message' => 'Vous devez créer un profil avant de postuler.'], 422);
        }

        $existe = Candidature::where('offre_id', $offre->id)
            ->where('profil_id', $profil->id)
            ->exists();

        if ($existe) {
            return response()->json(['message' => 'Vous avez déjà postulé à cette offre.'], 422);
        }

        $request->validate([
            'message' => 'nullable|string|max:2000',
        ]);

        $candidature = Candidature::create([
            'offre_id'  => $offre->id,
            'profil_id' => $profil->id,
            'message'   => $request->message,
            'statut'    => 'en_attente',
        ]);

        event(new CandidatureDeposee($candidature));

        return response()->json([
            'message'      => 'Candidature soumise avec succès.',
            'candidature'  => $candidature->load('offre'),
        ], 201);
    }



    public function mesCandidatures(Request $request)
    {
        $profil = $request->user()->profil;

        if (!$profil) {
            return response()->json(['message' => 'Aucun profil trouvé.'], 404);
        }

        $candidatures = Candidature::with(['offre.user'])
            ->where('profil_id', $profil->id)
            ->latest()
            ->get();

        return response()->json($candidatures);
    }


    public function candidaturesDuneOffre(Request $request, Offre $offre)
    {
        if ($offre->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès interdit.'], 403);
        }

        $candidatures = Candidature::with(['profil.user', 'profil.competences'])
            ->where('offre_id', $offre->id)
            ->latest()
            ->get();

        return response()->json($candidatures);
    }


    public function changerStatut(Request $request, Candidature $candidature)
    {
        if ($candidature->offre->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Accès interdit.'], 403);
        }

        $request->validate([
            'statut' => 'required|in:en_attente,acceptee,refusee',
        ]);

        $ancienStatut = $candidature->statut;
        $candidature->update(['statut' => $request->statut]);

        event(new StatutCandidatureMis($candidature, $ancienStatut));

        return response()->json([
            'message'     => 'Statut mis à jour.',
            'candidature' => $candidature,
        ]);
    }
}
