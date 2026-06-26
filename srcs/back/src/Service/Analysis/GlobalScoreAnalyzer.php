<?php

namespace App\Service\Analysis;

use App\DTO\Sync\SyncEventDTO;
use App\Entity\User;

/**
 * Couche 1 — Le "Jugement du Professeur".
 *
 * Calcule la note globale d'un module à partir des événements discovery.
 * Cette note pilote le SRS : elle détermine si l'intervalle s'allonge ou se raccourcit.
 *
 * Formule :
 *   score = (taux_réussite × 0.5) + (bonus_rapidité × 0.3) + (bonus_fluidité × 0.2)
 *
 *   taux_réussite : % d'exercices réussis au premier essai
 *   bonus_rapidité : pénalité si temps moyen > seuil (l'hésitation trahit la fragilité)
 *   bonus_fluidité : pénalité proportionnelle au nombre de passages en file de rattrapage
 */
class GlobalScoreAnalyzer
{
    // Seuil de temps de réponse "fluide" en millisecondes
    private const FAST_THRESHOLD_MS  = 3000;  // < 3s = réponse fluide
    private const SLOW_THRESHOLD_MS  = 8000;  // > 8s = hésitation marquée

    public function analyze(User $user, int $moduleId, array $events): float
    {
        if (empty($events)) {
            return 0.0;
        }

        $total      = count($events);
        $firstTryOk = 0;   // réussi du premier coup
        $catchupCount = 0; // passages en file de rattrapage (attempts > 1)
        $totalTimeMs  = 0;

        /** @var SyncEventDTO $event */
        foreach ($events as $event) {
            if ($event->success && $event->attempts === 1) {
                $firstTryOk++;
            }
            if ($event->attempts > 1) {
                $catchupCount++;
            }
            $totalTimeMs += $event->responseTimeMs;
        }

        $avgTimeMs = $totalTimeMs / $total;

        // ── Composante 1 : taux de réussite premier essai (50%) ──────────────
        $successRate = $firstTryOk / $total;

        // ── Composante 2 : bonus rapidité (30%) ──────────────────────────────
        // 1.0 si rapide, décroît linéairement jusqu'à 0.0 si très lent
        $speedScore = match(true) {
            $avgTimeMs <= self::FAST_THRESHOLD_MS => 1.0,
            $avgTimeMs >= self::SLOW_THRESHOLD_MS => 0.0,
            default => 1.0 - (($avgTimeMs - self::FAST_THRESHOLD_MS)
                             / (self::SLOW_THRESHOLD_MS - self::FAST_THRESHOLD_MS)),
        };

        // ── Composante 3 : bonus fluidité (20%) ──────────────────────────────
        // Pénalité proportionnelle aux rattrapages
        $catchupRate   = $catchupCount / $total;
        $fluencyScore  = max(0.0, 1.0 - $catchupRate);

        $globalScore = ($successRate * 0.5)
                     + ($speedScore  * 0.3)
                     + ($fluencyScore * 0.2);

        return round(min(1.0, max(0.0, $globalScore)), 3);
    }
}