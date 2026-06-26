<?php

namespace App\Service\Sync;

use App\DTO\Sync\SyncEventDTO;
use App\DTO\Sync\SyncPayloadDTO;

/**
 * Valide le payload JSON brut reçu du téléphone et le transforme en DTO typé.
 * C'est le premier rempart contre les données malformées.
 */
class SyncRequestValidator
{
    // Types d'items valides — correspondent aux tables existantes
    private const VALID_ITEM_TYPES = [
        'grapheme', 'word', 'subject', 'verb', 'complement'
    ];

    // Modes valides — inscrits dans la partition par le serveur
    private const VALID_MODES = ['discovery', 'review'];

    // Seuils de cohérence
    private const MAX_RESPONSE_TIME_MS = 300_000; // 5 minutes max par exercice
    private const MAX_ATTEMPTS         = 20;       // garde-fou contre les données corrompues
    private const MAX_EVENTS_PER_SYNC  = 200;      // max 200 exercices par session

    /**
     * @throws \InvalidArgumentException si le payload est invalide
     */
    public function validate(array $data): SyncPayloadDTO
    {
        // ── Champs racine ─────────────────────────────────────────────────────
        $this->requireString($data, 'lesson_id');
        $this->requirePositiveInt($data, 'module_id');
        $this->requireString($data, 'completed_at');
        $this->requireArray($data, 'events');

        // Validation de la date
        $completedAt = \DateTimeImmutable::createFromFormat(
            \DateTimeInterface::ATOM,
            $data['completed_at']
        );

        if ($completedAt === false) {
            throw new \InvalidArgumentException(
                'Le champ "completed_at" doit être au format ISO 8601 (ex: 2025-06-25T14:30:00+00:00).'
            );
        }

        // La date ne peut pas être dans le futur (tolérance de 5 minutes pour le décalage horaire)
        if ($completedAt > new \DateTimeImmutable('+5 minutes')) {
            throw new \InvalidArgumentException(
                'Le champ "completed_at" ne peut pas être dans le futur.'
            );
        }

        // ── Validation du nombre d'événements ────────────────────────────────
        if (count($data['events']) === 0) {
            throw new \InvalidArgumentException(
                'Le tableau "events" ne peut pas être vide.'
            );
        }

        if (count($data['events']) > self::MAX_EVENTS_PER_SYNC) {
            throw new \InvalidArgumentException(
                sprintf('Trop d\'événements : maximum %d par synchronisation.', self::MAX_EVENTS_PER_SYNC)
            );
        }

        // ── Validation de chaque événement ───────────────────────────────────
        $events = [];
        foreach ($data['events'] as $index => $raw) {
            $events[] = $this->validateEvent($raw, $index);
        }

        return new SyncPayloadDTO(
            lessonId:    $data['lesson_id'],
            moduleId:    (int) $data['module_id'],
            completedAt: $completedAt,
            events:      $events,
        );
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function validateEvent(mixed $raw, int $index): SyncEventDTO
    {
        $prefix = "events[$index]";

        if (!is_array($raw)) {
            throw new \InvalidArgumentException("$prefix doit être un objet.");
        }

        $this->requireString($raw, 'exercise_id', $prefix);
        $this->requireString($raw, 'item_type', $prefix);
        $this->requirePositiveInt($raw, 'item_id', $prefix);
        $this->requireString($raw, 'mode', $prefix);
        $this->requirePositiveInt($raw, 'source_module_id', $prefix);
        $this->requireNonNegativeInt($raw, 'response_time_ms', $prefix);
        $this->requirePositiveInt($raw, 'attempts', $prefix);
        $this->requireBool($raw, 'success', $prefix);

        // Validation des valeurs énumérées
        if (!in_array($raw['item_type'], self::VALID_ITEM_TYPES, true)) {
            throw new \InvalidArgumentException(
                "$prefix.item_type invalide : \"{$raw['item_type']}\". "
                . 'Valeurs acceptées : ' . implode(', ', self::VALID_ITEM_TYPES)
            );
        }

        if (!in_array($raw['mode'], self::VALID_MODES, true)) {
            throw new \InvalidArgumentException(
                "$prefix.mode invalide : \"{$raw['mode']}\". "
                . 'Valeurs acceptées : ' . implode(', ', self::VALID_MODES)
            );
        }

        // Validation des seuils de cohérence
        if ($raw['response_time_ms'] > self::MAX_RESPONSE_TIME_MS) {
            throw new \InvalidArgumentException(
                "$prefix.response_time_ms dépasse le maximum autorisé (" . self::MAX_RESPONSE_TIME_MS . "ms)."
            );
        }

        if ($raw['attempts'] > self::MAX_ATTEMPTS) {
            throw new \InvalidArgumentException(
                "$prefix.attempts dépasse le maximum autorisé (" . self::MAX_ATTEMPTS . ")."
            );
        }

        // Cohérence success/attempts : si success=false, on s'assure que attempts >= 1
        if (!$raw['success'] && $raw['attempts'] < 1) {
            throw new \InvalidArgumentException(
                "$prefix : un exercice échoué doit avoir au moins 1 tentative."
            );
        }

        return new SyncEventDTO(
            exerciseId:     $raw['exercise_id'],
            itemType:       $raw['item_type'],
            itemId:         (int) $raw['item_id'],
            mode:           $raw['mode'],
            sourceModuleId: (int) $raw['source_module_id'],
            responseTimeMs: (int) $raw['response_time_ms'],
            attempts:       (int) $raw['attempts'],
            success:        (bool) $raw['success'],
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers de validation
    // ─────────────────────────────────────────────────────────────────────────

    private function requireString(array $data, string $field, string $prefix = ''): void
    {
        $key = $prefix ? "$prefix.$field" : $field;
        if (!isset($data[$field]) || !is_string($data[$field]) || trim($data[$field]) === '') {
            throw new \InvalidArgumentException("Le champ \"$key\" est requis et doit être une chaîne non vide.");
        }
    }

    private function requirePositiveInt(array $data, string $field, string $prefix = ''): void
    {
        $key = $prefix ? "$prefix.$field" : $field;
        if (!isset($data[$field]) || !is_numeric($data[$field]) || (int) $data[$field] <= 0) {
            throw new \InvalidArgumentException("Le champ \"$key\" est requis et doit être un entier positif.");
        }
    }

    private function requireNonNegativeInt(array $data, string $field, string $prefix = ''): void
    {
        $key = $prefix ? "$prefix.$field" : $field;
        if (!isset($data[$field]) || !is_numeric($data[$field]) || (int) $data[$field] < 0) {
            throw new \InvalidArgumentException("Le champ \"$key\" est requis et doit être un entier >= 0.");
        }
    }

    private function requireArray(array $data, string $field, string $prefix = ''): void
    {
        $key = $prefix ? "$prefix.$field" : $field;
        if (!isset($data[$field]) || !is_array($data[$field])) {
            throw new \InvalidArgumentException("Le champ \"$key\" est requis et doit être un tableau.");
        }
    }

    private function requireBool(array $data, string $field, string $prefix = ''): void
    {
        $key = $prefix ? "$prefix.$field" : $field;
        if (!isset($data[$field]) || !is_bool($data[$field])) {
            throw new \InvalidArgumentException("Le champ \"$key\" est requis et doit être un booléen (true/false).");
        }
    }
}