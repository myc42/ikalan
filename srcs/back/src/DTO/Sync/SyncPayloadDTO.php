<?php

namespace App\DTO\Sync;

/**
 * Représente le payload complet envoyé par le téléphone lors d'une synchronisation.
 */
class SyncPayloadDTO
{
    /**
     * @param SyncEventDTO[] $events
     */
    public function __construct(
        public readonly string             $lessonId,
        public readonly int                $moduleId,
        public readonly \DateTimeImmutable $completedAt,
        public readonly array              $events,
    ) {}

    /**
     * Retourne uniquement les événements discovery (pour le module courant).
     *
     * @return SyncEventDTO[]
     */
    public function getDiscoveryEvents(): array
    {
        return array_values(array_filter(
            $this->events,
            fn(SyncEventDTO $e) => $e->mode === 'discovery'
        ));
    }

    /**
     * Retourne uniquement les événements review (pour les modules antérieurs).
     *
     * @return SyncEventDTO[]
     */
    public function getReviewEvents(): array
    {
        return array_values(array_filter(
            $this->events,
            fn(SyncEventDTO $e) => $e->mode === 'review'
        ));
    }

    /**
     * Regroupe les événements review par source_module_id.
     * Permet de traiter chaque module révisé indépendamment.
     *
     * @return array<int, SyncEventDTO[]>
     */
    public function getReviewEventsByModule(): array
    {
        $byModule = [];
        foreach ($this->getReviewEvents() as $event) {
            $byModule[$event->sourceModuleId][] = $event;
        }
        return $byModule;
    }
}