<?php

namespace App\DTO\Sync;

/**
 * Représente un événement brut envoyé par le téléphone.
 * Un événement = une réponse à un exercice.
 */
class SyncEventDTO
{
    public function __construct(
        public readonly string $exerciseId,
        public readonly string $itemType,       // grapheme | word | subject | verb | complement
        public readonly int    $itemId,
        public readonly string $mode,           // discovery | review
        public readonly int    $sourceModuleId,
        public readonly int    $responseTimeMs,
        public readonly int    $attempts,       // nombre de tentatives (1 = direct, >1 = rattrapage)
        public readonly bool   $success,
    ) {}
}