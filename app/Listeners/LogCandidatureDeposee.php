<?php

namespace App\Listeners;

use App\Events\CandidatureDeposee;
use Illuminate\Support\Facades\Log;

class LogCandidatureDeposee
{
    public function handle(CandidatureDeposee $event): void
    {
        $candidature = $event->candidature->load(['profil.user', 'offre']);
        $nomCandidat = $candidature->profil->user->name;
        $titreOffre  = $candidature->offre->titre;
        $date        = now()->format('d/m/Y H:i:s');

        Log::channel('candidatures')->info(
            "[{$date}] Nouvelle candidature : {$nomCandidat} → \"{$titreOffre}\""
        );
    }
}
