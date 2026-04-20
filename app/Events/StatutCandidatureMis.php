<?php

namespace App\Events;

use App\Models\Candidature;
use Illuminate\Foundation\Events\Dispatchable;

class StatutCandidatureMis
{
    use Dispatchable;

    public function __construct(
        public Candidature $candidature,
        public string $ancienStatut
    ) {}
}
