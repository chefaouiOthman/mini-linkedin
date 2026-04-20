<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function listeUsers()
    {
        $users = User::with('profil')->latest()->get();
        return response()->json($users);
    }


    public function supprimerUser(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json(['message' => 'Impossible de supprimer un administrateur.'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'Compte supprimé avec succès.']);
    }


    public function toggleOffre(Offre $offre)
    {
        $offre->update(['actif' => !$offre->actif]);

        $etat = $offre->actif ? 'activée' : 'désactivée';
        return response()->json([
            'message' => "Offre {$etat} avec succès.",
            'offre'   => $offre,
        ]);
    }
}
