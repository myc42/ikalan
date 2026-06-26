<?php

namespace App\Service\Analysis;

use App\DTO\Sync\SyncEventDTO;
use App\Entity\User;
use App\Entity\UserItemMastery;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Couche 2 — Le "Microscope".
 *
 * Met à jour user_item_mastery pour chaque item rencontré dans la session.
 * Utilise un UPSERT : crée la ligne si elle n'existe pas, met à jour sinon.
 *
 * Formule de mise à jour du mastery_score (moyenne glissante pondérée) :
 *   nouveau_score = (ancien_score × 0.7) + (score_session × 0.3)
 *
 * Le poids 0.7/0.3 favorise la stabilité : un bon résultat ponctuel
 * ne masque pas une faiblesse ancienne, et inversement.
 */
class ItemMasteryAnalyzer
{
    // Score par événement selon le résultat
    private const SCORE_SUCCESS_FIRST_TRY = 1.0;  // réussi du premier coup
    private const SCORE_SUCCESS_CATCHUP   = 0.5;  // réussi après rattrapage
    private const SCORE_FAILURE           = 0.0;  // non réussi (force_pass)

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * @param SyncEventDTO[] $events
     */
    public function analyze(User $user, array $events): void
    {
        // Regroupe les events par (item_type, item_id) pour traiter chaque item une seule fois
        $grouped = $this->groupByItem($events);

        foreach ($grouped as $key => [$itemType, $itemId, $itemEvents]) {
            $this->updateMastery($user, $itemType, $itemId, $itemEvents);
        }

        $this->em->flush();
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function groupByItem(array $events): array
    {
        $groups = [];

        /** @var SyncEventDTO $event */
        foreach ($events as $event) {
            $key = $event->itemType . '_' . $event->itemId;
            if (!isset($groups[$key])) {
                $groups[$key] = [$event->itemType, $event->itemId, []];
            }
            $groups[$key][2][] = $event;
        }

        return $groups;
    }

    /**
     * @param SyncEventDTO[] $itemEvents
     */
    private function updateMastery(User $user, string $itemType, int $itemId, array $itemEvents): void
    {
        // Cherche la ligne existante ou en crée une nouvelle
        $mastery = $this->em->getRepository(UserItemMastery::class)->findOneBy([
            'userId'   => $user,
            'itemType' => $itemType,
            'itemId'   => (string) $itemId,
        ]);

        if ($mastery === null) {
            $mastery = new UserItemMastery();
            $mastery->setUserId($user);
            $mastery->setItemType($itemType);
            $mastery->setItemId((string) $itemId);
            $mastery->setMasteryScore('0.500');
            $mastery->setAvgResponseMs(0);
            $mastery->setErrorCount(0);
            $this->em->persist($mastery);
        }

        // Calcul du score moyen pour cette session sur cet item
        $sessionScore  = 0.0;
        $totalTimeMs   = 0;
        $errorCount    = 0;

        /** @var SyncEventDTO $event */
        foreach ($itemEvents as $event) {
            $sessionScore += match(true) {
                $event->success && $event->attempts === 1 => self::SCORE_SUCCESS_FIRST_TRY,
                $event->success && $event->attempts > 1   => self::SCORE_SUCCESS_CATCHUP,
                default                                    => self::SCORE_FAILURE,
            };

            $totalTimeMs += $event->responseTimeMs;

            if (!$event->success || $event->attempts > 1) {
                $errorCount++;
            }
        }

        $sessionScore /= count($itemEvents);

        // Moyenne glissante pondérée : stabilité 70% / session 30%
        $oldScore    = (float) $mastery->getMasteryScore();
        $newScore    = ($oldScore * 0.7) + ($sessionScore * 0.3);
        $newScore    = round(min(1.0, max(0.0, $newScore)), 3);

        // Moyenne glissante du temps de réponse
        $oldAvgMs    = $mastery->getAvgResponseMs();
        $sessionAvgMs = (int) ($totalTimeMs / count($itemEvents));
        $newAvgMs    = $oldAvgMs === 0
            ? $sessionAvgMs
            : (int) (($oldAvgMs * 0.7) + ($sessionAvgMs * 0.3));

        $mastery->setMasteryScore((string) $newScore);
        $mastery->setAvgResponseMs($newAvgMs);
        $mastery->setErrorCount($mastery->getErrorCount() + $errorCount);
        $mastery->setLastSeenAt(new \DateTimeImmutable());
    }
}