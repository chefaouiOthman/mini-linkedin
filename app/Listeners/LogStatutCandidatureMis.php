<?php

namespace App\Listeners;

use App\Events\StatutCandidatureMis;
use Illuminate\Support\Facades\Log;

class LogStatutCandidatureMis
{
    public function handle(StatutCandidatureMis $event): void
    {
        $candidature  = $event->candidature->load(['profil.user', 'offre']);
        $nomCandidat  = $candidature->profil->user->name;
        $ancienStatut = $event->ancienStatut;
        $nouveauStatut = $candidature->statut;
        $date         = now()->format('d/m/Y H:i:s');

        Log::channel('candidatures')->info(
            "[{$date}] Statut modifié pour {$nomCandidat} : {$ancienStatut} → {$nouveauStatut}"
        );
    }
}
