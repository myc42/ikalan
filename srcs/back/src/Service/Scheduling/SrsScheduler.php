<?php

namespace App\Service\Scheduling;

use App\Entity\Modules;
use App\Entity\User;
use App\Entity\UserModuleProgress;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UserModuleStatus;

/**
 * Calcule les fenêtres de révision SRS selon l'algorithme SM-2 simplifié.
 *
 * Principe :
 *   - Un bon score → l'intervalle s'allonge (on révise moins souvent)
 *   - Un mauvais score → l'intervalle se raccourcit (on révise plus vite)
 *   - La fenêtre = [target - marge_début, target + marge_fin]
 *     pour absorber les jours sans connexion
 */
class SrsScheduler
{
    // Seuils de score
    private const SCORE_PERFECT   = 0.90; // maîtrise excellente
    private const SCORE_GOOD      = 0.70; // maîtrise correcte
    private const SCORE_WEAK      = 0.50; // fragile

    // Facteur de facilité : bornes SM-2
    private const EASE_MIN        = 1.3;
    private const EASE_MAX        = 3.0;
    private const EASE_DEFAULT    = 2.5;

    // Fenêtre de tolérance en jours autour de la date cible
    private const WINDOW_BEFORE   = 1; // peut commencer 1 jour avant la cible
    private const WINDOW_AFTER    = 3; // reste valide 3 jours après la cible

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * Met à jour ou crée l'entrée user_module_progress pour ce module,
     * et retourne les nouvelles valeurs SRS calculées.
     */
    public function schedule(User $user, int $moduleId, float $globalScore): array
    {
        $module   = $this->em->getReference(Modules::class, $moduleId);
        $progress = $this->em->getRepository(UserModuleProgress::class)->findOneBy([
            'userId'   => $user,
            'moduleId' => $module,
        ]);

        if ($progress === null) {
            $progress = new UserModuleProgress();
            $progress->setUserId($user);
            $progress->setModuleId($module);
            $progress->setEaseFactor((string) self::EASE_DEFAULT);
            $progress->setIntervalDays(1);
            $progress->setConsecutivePerfectScores(0);
            $this->em->persist($progress);
        }

        // ── Calcul du nouvel ease_factor (SM-2) ──────────────────────────────
        $oldEase    = (float) $progress->getEaseFactor();
        $newEase    = $this->computeEaseFactor($oldEase, $globalScore);

        // ── Calcul du nouvel intervalle ───────────────────────────────────────
        $oldInterval   = $progress->getIntervalDays();
        $newInterval   = $this->computeInterval($oldInterval, $newEase, $globalScore);

        // ── Calcul des scores consécutifs parfaits ────────────────────────────
        $consecutive = $globalScore >= self::SCORE_PERFECT
            ? $progress->getConsecutivePerfectScores() + 1
            : 0;

        // ── Calcul de la fenêtre ──────────────────────────────────────────────
        $today       = new \DateTimeImmutable('today');
        $target      = $today->modify("+{$newInterval} days");
        $windowStart = $target->modify('-' . self::WINDOW_BEFORE . ' days');
        $windowEnd   = $target->modify('+' . self::WINDOW_AFTER . ' days');

        // ── Statut ────────────────────────────────────────────────────────────
       $status = match(true) {
    $consecutive >= 3                     => UserModuleStatus::DONE,
    $globalScore >= self::SCORE_GOOD      => UserModuleStatus::IN_PROGRESS,
    default                               => UserModuleStatus::REVIEW,
};

        // ── Mise à jour ───────────────────────────────────────────────────────
        $progress->setGlobalScore((string) $globalScore);
        $progress->setEaseFactor((string) $newEase);
        $progress->setIntervalDays($newInterval);
        $progress->setTargetAt($target);
        $progress->setWindowStartAt($windowStart);
        $progress->setWindowEndAt($windowEnd);
        $progress->setStatus($status);
        $progress->setConsecutivePerfectScores($consecutive);
        $progress->setLastSeenAt(new \DateTimeImmutable());

        // Pas de flush ici — SyncIngestionService fait le flush global à la fin

        return [
            'ease_factor'  => $newEase,
            'interval_days'=> $newInterval,
            'window_start' => $windowStart,
            'window_end'   => $windowEnd,
            'status'       => $status,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Ajuste le facteur de facilité selon le score (formule SM-2 adaptée).
     * Bon score → ease monte → intervalles plus longs
     * Mauvais score → ease descend → révisions plus fréquentes
     */
    private function computeEaseFactor(float $oldEase, float $score): float
    {
        // Formule SM-2 : EF' = EF + (0.1 - (1-score) × (0.08 + (1-score) × 0.02))
        $delta   = 0.1 - (1 - $score) * (0.08 + (1 - $score) * 0.02);
        $newEase = $oldEase + $delta;

        return round(min(self::EASE_MAX, max(self::EASE_MIN, $newEase)), 3);
    }

    /**
     * Calcule le prochain intervalle en jours.
     */
    private function computeInterval(int $oldInterval, float $easeFactor, float $score): int
    {
        if ($score < self::SCORE_WEAK) {
            // Score trop faible → on repart à 1 jour (révision immédiate)
            return 1;
        }

        if ($oldInterval === 1) {
            // Première révision réussie → 3 jours
            return 3;
        }

        if ($oldInterval === 3) {
            // Deuxième révision → 7 jours
            return 7;
        }

        // Intervalles suivants : on multiplie par l'ease_factor
        return (int) round($oldInterval * $easeFactor);
    }
}