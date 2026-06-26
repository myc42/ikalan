<?php

namespace App\Service\Lesson;

use App\Entity\Modules;

/**
 * Applique les 3 règles pédagogiques avant d'envoyer la leçon au téléphone.
 *
 * Règle 1 — Proportion    : au moins 70 % de discovery, max 30 % de review
 * Règle 2 — Respiration   : deux exercices de review du même item ne se suivent jamais
 * Règle 3 — Canal sensoriel : on n'injecte pas une révision auditive au milieu
 *                             d'un bloc moteur (tracer), et vice-versa
 */
class ExerciseWeaver
{
    private const DISCOVERY_MIN_RATIO = 0.70;

    public function weave(array $exercises, Modules $module): array
    {
        $discovery = array_values(array_filter($exercises, fn($e) => $e['mode'] === 'discovery'));
        $review    = array_values(array_filter($exercises, fn($e) => $e['mode'] === 'review'));

        // ── Règle 1 : Proportion ─────────────────────────────────────────────
        $total        = count($discovery) + count($review);
        $maxReview    = (int) floor($total * (1 - self::DISCOVERY_MIN_RATIO));
        $review       = array_slice($review, 0, $maxReview);

        // ── Règle 2 : Respiration ────────────────────────────────────────────
        // On intercale les reviews dans les discovery en les espaçant
        $woven = $this->interleave($discovery, $review);

        // ── Règle 3 : Canal sensoriel ────────────────────────────────────────
        // On vérifie qu'une révision auditive ne tombe pas entre deux exercices
        // moteurs (channel = 'motor') et on la déplace si nécessaire
        $woven = $this->respectChannelCoherence($woven);

        // On réindexe les positions finales
        return $this->reindex($woven);
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Intercale les reviews dans les discovery de façon régulière.
     * Ex : 6 discovery + 2 review → D D D R D D D R
     */
    private function interleave(array $discovery, array $review): array
    {
        if (empty($review)) {
            return $discovery;
        }

        $result   = [];
        $dCount   = count($discovery);
        $rCount   = count($review);
        $step     = (int) floor($dCount / ($rCount + 1));
        $rIndex   = 0;
        $stepCount = 0;

        foreach ($discovery as $d) {
            $result[] = $d;
            $stepCount++;

            // Tous les $step exercices de discovery, on injecte un review
            if ($stepCount >= $step && $rIndex < $rCount) {
                $result[] = $review[$rIndex];
                $rIndex++;
                $stepCount = 0;
            }
        }

        // S'il reste des reviews non placés, on les ajoute à la fin
        while ($rIndex < $rCount) {
            $result[] = $review[$rIndex];
            $rIndex++;
        }

        return $result;
    }

    /**
     * Règle 3 : si un exercice de review audio se retrouve entre deux exercices
     * motor, on le déplace après le bloc motor.
     */
    private function respectChannelCoherence(array $exercises): array
    {
        $count = count($exercises);

        for ($i = 1; $i < $count - 1; $i++) {
            $prev = $exercises[$i - 1];
            $curr = $exercises[$i];
            $next = $exercises[$i + 1];

            $isReviewAudio = $curr['mode'] === 'review' && $curr['channel'] === 'audio';
            $surroundedByMotor = ($prev['channel'] ?? '') === 'motor'
                              && ($next['channel'] ?? '') === 'motor';

            if ($isReviewAudio && $surroundedByMotor) {
                // On échange cet exercice avec le suivant pour sortir du bloc motor
                [$exercises[$i], $exercises[$i + 1]] = [$exercises[$i + 1], $exercises[$i]];
            }
        }

        return $exercises;
    }

    private function reindex(array $exercises): array
    {
        $position = 1;
        foreach ($exercises as &$exercise) {
            $exercise['position'] = $position++;
        }
        return array_values($exercises);
    }
}