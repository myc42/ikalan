<?php

namespace App\Service\Lesson;

use App\Entity\Modules;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Orchestre la construction d'une leçon complète.
 *
 * Logique :
 *  - Si le module n'a PAS de subjects/verbs/complements
 *      → récupère le graphème du module + jusqu'à 5 mots filtrés
 *        par word_level ET par les graphèmes déjà maîtrisés par l'apprenant
 *  - Si le module EN A
 *      → assemble des phrases dynamiques (sujet + verbe + complément)
 */
class LessonComposer
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ExerciseWeaver        $weaver,
        private readonly PayloadSerializer     $serializer,
    ) {}

    public function compose(User $user, Modules $module): array
    {
        $hasPhrase = $this->moduleHasPhraseContent($module);

        if ($hasPhrase) {
            $exercises = $this->buildPhraseExercises($module);
        } else {
            $exercises = $this->buildGraphemeAndWordExercises($user, $module);
        }

        $woven = $this->weaver->weave($exercises, $module);

        return $this->serializer->serialize($user, $module, $woven);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Branche 1 : module graphème + mots
    // ─────────────────────────────────────────────────────────────────────────
private function buildGraphemeAndWordExercises(User $user, Modules $module): array
{
    $exercises = [];

    // 1. Tous les graphèmes du module
    $graphemes = $this->fetchGraphemesForModule($module);

    foreach ($graphemes as $grapheme) {
        $exercises[] = [
            'source'           => 'grapheme',
            'data'             => $grapheme,
            'mode'             => 'discovery',
            'channel'          => 'audio',
            'source_module_id' => $module->getId(),
        ];
    }

    // 2. Mots filtrés selon word_level et graphèmes connus
    $wordLevel = $module->getWordLevel();
    $words = $wordLevel !== null
        ? $this->fetchWordsForLearner($user, $wordLevel, $module)
        : [];

    foreach ($words as $word) {
        $exercises[] = [
            'source'           => 'word',
            'data'             => $word,
            'mode'             => 'discovery',
            'channel'          => 'audio',
            'source_module_id' => $module->getId(),
        ];
    }

    return $exercises;
}
    // ─────────────────────────────────────────────────────────────────────────
    // Branche 2 : module phrases (subjects + verbs + complements)
    // ─────────────────────────────────────────────────────────────────────────

    private function buildPhraseExercises(Modules $module): array
    {
        $exercises = [];

        $subjects    = $this->fetchSubjectsForModule($module);
        $verbs       = $this->fetchVerbsForModule($module);
        $complements = $this->fetchComplementsForModule($module);

        $count = 0;
        foreach ($subjects as $subject) {
            foreach ($verbs as $verb) {
                foreach ($complements as $complement) {
                    if ($count >= 5) break 3;

                    $exercises[] = [
                        'source' => 'phrase',
                        'data'   => [
                            'subject'    => $subject,
                            'verb'       => $verb,
                            'complement' => $complement,
                        ],
                        'mode'             => 'discovery',
                        'channel'          => 'audio',
                        'source_module_id' => $module->getId(),
                    ];

                    $count++;
                }
            }
        }

        return $exercises;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Requêtes SQL natives
    // ─────────────────────────────────────────────────────────────────────────

    private function moduleHasPhraseContent(Modules $module): bool
    {
        $count = (int) $this->em->getConnection()->fetchOne('
            SELECT COUNT(ms.id)
            FROM module_subjects ms
            WHERE ms.module_id_id = :moduleId
        ', ['moduleId' => $module->getId()]);

        return $count > 0;
    }
private function fetchGraphemesForModule(Modules $module): array
{
    return $this->em->getConnection()->fetchAllAssociative('
        SELECT
            g.id,
            g.min,
            g.maj,
            g.word,
            g.phonetic_list,
            g.is_silent_letter,
            af.storage_key   AS audio_key,
            af.duration_ms   AS audio_duration_ms,
            gt.name          AS type_name
        FROM graphemes g
        LEFT JOIN audio_files af    ON af.id = g.audio_path_id
        LEFT JOIN grapheme_types gt ON gt.id = g.type_id_id
        WHERE g.module_id_id = :moduleId
        ORDER BY g.id ASC
    ', ['moduleId' => $module->getId()]);
}


   private function fetchWordsForLearner(User $user, int $wordLevel, Modules $module): array
{
    // Étape 1a : graphèmes déjà maîtrisés dans les sessions précédentes
    $masteredGraphemes = $this->em->getConnection()->fetchFirstColumn('
        SELECT g.min
        FROM user_item_mastery uim
        INNER JOIN graphemes g ON g.id = CAST(uim.item_id AS integer)
        WHERE uim.user_id_id    = :userId
          AND uim.item_type     = :itemType
          AND uim.mastery_score >= :threshold
    ', [
        'userId'    => $user->getId(),
        'itemType'  => 'grapheme',
        'threshold' => '0.5',
    ]);

    // Étape 1b : graphèmes du module en cours (découverts aujourd'hui)
    $currentGraphemes = $this->em->getConnection()->fetchFirstColumn('
        SELECT g.min
        FROM graphemes g
        WHERE g.module_id_id = :moduleId
    ', ['moduleId' => $module->getId()]);

    // Union des deux listes — sans doublons
    $knownGraphemes = array_unique(array_merge($masteredGraphemes, $currentGraphemes));

    if (empty($knownGraphemes)) {
        return [];
    }

    // Étapes 2 et 3 : identiques à avant
    $candidates = $this->em->getConnection()->fetchAllAssociative('
        SELECT
            w.id,
            w.word,
            w.segmentation,
            w.phonetic_list,
            w.is_syllable,
            w.is_sight_word,
            af.storage_key AS audio_key,
            af.duration_ms AS audio_duration_ms
        FROM words w
        LEFT JOIN audio_files af ON af.id = w.audio_path_id
        WHERE w.level_id_id = :levelId
        ORDER BY w.id ASC
        LIMIT 20
    ', ['levelId' => $wordLevel]);

    $filtered = [];
    foreach ($candidates as $word) {
        $segments = $this->parsePostgresArray($word['segmentation']);

        $allKnown = true;
        foreach ($segments as $segment) {
            if (!in_array($segment, $knownGraphemes, true)) {
                $allKnown = false;
                break;
            }
        }

        if ($allKnown) {
            $filtered[] = $word;
        }

        if (count($filtered) >= 5) {
            break;
        }
    }

    return $filtered;
}
    /**
     * Convertit le format PostgreSQL "{p,a,p,a}" en tableau PHP ["p","a","p","a"].
     */
    private function parsePostgresArray(string $pgArray): array
    {
        $clean = trim($pgArray, '{}');
        if ($clean === '') {
            return [];
        }
        return explode(',', $clean);
    }

    private function fetchSubjectsForModule(Modules $module): array
    {
        return $this->em->getConnection()->fetchAllAssociative('
            SELECT
                s.id,
                s.name,
                s.phonotique_list,
                af.storage_key AS audio_key
            FROM module_subjects ms
            INNER JOIN subjects s     ON s.id  = ms.subject_id_id
            LEFT  JOIN audio_files af ON af.id = s.audio_path_id
            WHERE ms.module_id_id = :moduleId
        ', ['moduleId' => $module->getId()]);
    }

    private function fetchVerbsForModule(Modules $module): array
    {
        return $this->em->getConnection()->fetchAllAssociative('
            SELECT
                v.id,
                v.name,
                v.phonotique_list,
                af.storage_key AS audio_key
            FROM module_verbs mv
            INNER JOIN verbs v        ON v.id  = mv.verb_id_id
            LEFT  JOIN audio_files af ON af.id = v.audio_path_id_id
            WHERE mv.module_id_id = :moduleId
        ', ['moduleId' => $module->getId()]);
    }

    private function fetchComplementsForModule(Modules $module): array
    {
        return $this->em->getConnection()->fetchAllAssociative('
            SELECT
                c.id,
                c.name,
                c.phonotique_list,
                af.storage_key AS audio_key
            FROM module_complements mc
            INNER JOIN complements c  ON c.id  = mc.complement_id_id
            LEFT  JOIN audio_files af ON af.id = c.audio_path_id
            WHERE mc.module_id_id = :moduleId
        ', ['moduleId' => $module->getId()]);
    }
}