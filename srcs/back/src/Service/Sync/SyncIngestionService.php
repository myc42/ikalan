<?php

namespace App\Service\Sync;

use App\DTO\Sync\SyncPayloadDTO;
use App\Entity\SessionLog;
use App\Entity\User;
use App\Service\Analysis\GlobalScoreAnalyzer;
use App\Service\Analysis\ItemMasteryAnalyzer;
use App\Service\Scheduling\SrsScheduler;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Orchestre le traitement complet d'une synchronisation.
 *
 * Ordre d'exécution :
 *  1. Persiste le SessionLog (carnet de bord brut)
 *  2. GlobalScoreAnalyzer  → note globale du module courant (Couche 1)
 *  3. ItemMasteryAnalyzer  → maîtrise par item (Couche 2)
 *  4. SrsScheduler         → calcule les fenêtres de révision
 *  5. Marque le SessionLog comme traité
 */
class SyncIngestionService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly GlobalScoreAnalyzer   $globalScoreAnalyzer,
        private readonly ItemMasteryAnalyzer   $itemMasteryAnalyzer,
        private readonly SrsScheduler          $srsScheduler,
    ) {}

    public function process(User $user, SyncPayloadDTO $payload): array
    {
        // ── 1. Persist le carnet de bord brut ────────────────────────────────
        // On sauvegarde TOUT avant d'analyser — si une analyse plante,
        // les données brutes sont déjà en sécurité
        $sessionLog = $this->persistSessionLog($user, $payload);

        // ── 2. Couche 1 : note globale du module courant (events discovery) ──
        $globalScore = $this->globalScoreAnalyzer->analyze(
            $user,
            $payload->moduleId,
            $payload->getDiscoveryEvents()
        );

        // ── 3. Couche 2 : maîtrise par item (tous les events) ────────────────
        $this->itemMasteryAnalyzer->analyze($user, $payload->events);

        // ── 4. SRS : calcule les fenêtres pour le module courant ──────────────
        $srsResult = $this->srsScheduler->schedule(
            $user,
            $payload->moduleId,
            $globalScore
        );

        // ── 5. Traiter également les modules révisés (events review) ─────────
        foreach ($payload->getReviewEventsByModule() as $moduleId => $reviewEvents) {
            $reviewScore = $this->globalScoreAnalyzer->analyze(
                $user,
                $moduleId,
                $reviewEvents
            );
            $this->srsScheduler->schedule($user, $moduleId, $reviewScore);
        }

        // ── 6. Marquer le SessionLog comme traité ────────────────────────────
        $sessionLog->setProcessedAt(new \DateTimeImmutable());
        $this->em->flush();

        // ── 7. Retourner le résumé du traitement ─────────────────────────────
        return [
            'session_log_id'   => $sessionLog->getId(),
            'module_id'        => $payload->moduleId,
            'events_processed' => count($payload->events),
            'discovery_count'  => count($payload->getDiscoveryEvents()),
            'review_count'     => count($payload->getReviewEvents()),
            'global_score'     => round($globalScore, 3),
            'next_window'      => [
                'start' => $srsResult['window_start']->format('Y-m-d'),
                'end'   => $srsResult['window_end']->format('Y-m-d'),
            ],
            'ease_factor'      => round($srsResult['ease_factor'], 3),
            'interval_days'    => $srsResult['interval_days'],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function persistSessionLog(User $user, SyncPayloadDTO $payload): SessionLog
    {
        $module = $this->em->getReference(\App\Entity\Modules::class, $payload->moduleId);

        $log = new SessionLog();
        $log->setUserId($user);
        $log->setModuleId($module);
        $log->setRawEvents($this->serializeEvents($payload));
        $log->setReceivedAt(new \DateTimeImmutable());

        $this->em->persist($log);
        $this->em->flush(); // flush immédiat pour avoir l'ID du log

        return $log;
    }

    /**
     * Sérialise le payload complet pour l'archivage dans raw_events.
     * On stocke tout — le téléphone a signé chaque événement avec mode
     * et source_module_id, ce qui permet une analyse rétrospective.
     */
    private function serializeEvents(SyncPayloadDTO $payload): array
    {
        return [
            'lesson_id'    => $payload->lessonId,
            'completed_at' => $payload->completedAt->format(\DateTimeInterface::ATOM),
            'events'       => array_map(fn($e) => [
                'exercise_id'      => $e->exerciseId,
                'item_type'        => $e->itemType,
                'item_id'          => $e->itemId,
                'mode'             => $e->mode,
                'source_module_id' => $e->sourceModuleId,
                'response_time_ms' => $e->responseTimeMs,
                'attempts'         => $e->attempts,
                'success'          => $e->success,
            ], $payload->events),
        ];
    }
}