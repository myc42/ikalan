<?php

namespace App\Service\Lesson;

use App\Entity\Modules;
use App\Entity\User;

/**
 * Transforme la liste d'exercices tissés en payload JSON final.
 * C'est le seul endroit qui connaît la structure exacte de la "partition"
 * envoyée au téléphone.
 */
class PayloadSerializer
{
    public function serialize(User $user, Modules $module, array $wovенExercises): array
    {
        $exercises     = [];
        $mediaManifest = [];
        $seenAudioKeys = [];

        foreach ($wovенExercises as $raw) {
            $exercise = match ($raw['source']) {
                'grapheme' => $this->serializeGrapheme($raw),
                'word'     => $this->serializeWord($raw),
                'phrase'   => $this->serializePhrase($raw),
                default    => null,
            };

            if ($exercise === null) continue;

            $exercises[] = $exercise;

            // Collecte des médias uniques pour le media_manifest
            $this->collectAudioKeys($raw, $seenAudioKeys, $mediaManifest);
        }

        return [
            // Identifiant unique de la leçon (utilisé par le téléphone
            // pour associer ses raw_events à la bonne session)
            'lesson_id'     => uniqid('lesson_', true),
            'module_id'     => $module->getId(),
            'module_title'  => $module->getTitle(),
            'generated_at'  => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),

            'exercises'     => $exercises,

            // Règle de sécurité embarquée : le téléphone force la réussite
            // après 3 échecs consécutifs sur le même exercice (Chapitre 9)
            'catchup_rules' => [
                'max_retries_before_force_pass' => 3,
            ],

            // Liste des fichiers audio à pré-télécharger
            // Le téléphone n'a besoin que de ce qui est dans cette liste
            'media_manifest' => $mediaManifest,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function serializeGrapheme(array $raw): array
    {
        $g = $raw['data'];

        return [
            'id'               => sprintf('ex-g-%s', $g['id']),
            'position'         => $raw['position'],
            'type'             => 'listen_and_choose',
            'item_type'        => 'grapheme',
            'item_id'          => (int) $g['id'],
            'mode'             => $raw['mode'],
            'source_module_id' => $raw['source_module_id'],
            'channel'          => $raw['channel'],

            // Ce que le téléphone affiche
            'display'          => $g['min'],
            'display_maj'      => $g['maj'],
            'phonetic'         => $g['phonetic_list'] ?? [],
            'audio_key'        => $g['audio_key'],
            'audio_duration_ms'=> (int) ($g['audio_duration_ms'] ?? 0),

            // Règles d'affichage conditionnelles pré-embarquées
            // Le téléphone les exécute sans comprendre la pédagogie
            'display_rules'    => [
                'slow_threshold_ms' => 10000,
                'slow_message'      => 'Prends ton temps, tu vas y arriver !',
            ],
        ];
    }

    private function serializeWord(array $raw): array
    {
        $w = $raw['data'];

        return [
            'id'               => sprintf('ex-w-%s', $w['id']),
            'position'         => $raw['position'],
            'type'             => 'listen_and_choose',
            'item_type'        => 'word',
            'item_id'          => (int) $w['id'],
            'mode'             => $raw['mode'],
            'source_module_id' => $raw['source_module_id'],
            'channel'          => $raw['channel'],

            'display'          => $w['word'],
            'segmentation'     => $w['segmentation'] ?? [],
            'phonetic'         => $w['phonetic_list'] ?? [],
            'is_syllable'      => (bool) $w['is_syllable'],
            'is_sight_word'    => (bool) $w['is_sight_word'],
            'audio_key'        => $w['audio_key'],
            'audio_duration_ms'=> (int) ($w['audio_duration_ms'] ?? 0),

            'display_rules'    => [
                'slow_threshold_ms' => 12000,
                'slow_message'      => 'Écoute bien, tu peux le faire !',
            ],
        ];
    }

    private function serializePhrase(array $raw): array
    {
        $subject    = $raw['data']['subject'];
        $verb       = $raw['data']['verb'];
        $complement = $raw['data']['complement'];

        // La phrase complète est l'assemblage des 3 composants
        $fullPhrase = implode(' ', array_filter([
            $subject['name']    ?? null,
            $verb['name']       ?? null,
            $complement['name'] ?? null,
        ]));

        return [
            'id'               => sprintf('ex-p-%s-%s-%s',
                $subject['id'], $verb['id'], $complement['id']
            ),
            'position'         => $raw['position'],
            'type'             => 'listen_and_repeat',
            'item_type'        => 'phrase',
            'mode'             => $raw['mode'],
            'source_module_id' => $raw['source_module_id'],
            'channel'          => $raw['channel'],

            'display'          => $fullPhrase,
            'components'       => [
                'subject'    => ['id' => (int) $subject['id'],    'label' => $subject['name'],    'audio_key' => $subject['audio_key']],
                'verb'       => ['id' => (int) $verb['id'],       'label' => $verb['name'],       'audio_key' => $verb['audio_key']],
                'complement' => ['id' => (int) $complement['id'], 'label' => $complement['name'], 'audio_key' => $complement['audio_key']],
            ],

            'display_rules' => [
                'slow_threshold_ms' => 15000,
                'slow_message'      => 'Écoute la phrase entière avant de répondre.',
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function collectAudioKeys(array $raw, array &$seen, array &$manifest): void
    {
        $keys = [];

        if ($raw['source'] === 'phrase') {
            foreach (['subject', 'verb', 'complement'] as $part) {
                $key = $raw['data'][$part]['audio_key'] ?? null;
                if ($key) $keys[] = $key;
            }
        } else {
            $key = $raw['data']['audio_key'] ?? null;
            if ($key) $keys[] = $key;
        }

        foreach ($keys as $key) {
            if (!in_array($key, $seen, true)) {
                $seen[]     = $key;
                $manifest[] = ['key' => $key, 'type' => 'audio'];
            }
        }
    }
}