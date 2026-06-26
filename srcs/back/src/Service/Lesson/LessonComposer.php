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
 *      → récupère le grapheme + jusqu'à 5 words du niveau déduit via Progression
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

        // 1. Graphème principal du module
        $grapheme = $this->fetchGraphemeForModule($module);

        if ($grapheme !== null) {
            $exercises[] = [
                'source'           => 'grapheme',
                'data'             => $grapheme,
                'mode'             => 'discovery',
                'channel'          => 'audio',
                'source_module_id' => $module->getId(),
            ];
        }

        // 2. Niveau déduit depuis la Progression de l'apprenant
        //    Progression → moduleId → chapterId → chapter_order = niveau actuel
        //    Si aucune progression → niveau 1 par défaut (premier lancement)
        $levelId = $this->resolveLevelFromProgression($user, $module);
        $words   = $this->fetchWordsForLevel(levelId: $levelId, limit: 5);

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
    // Résolution du niveau via Progression
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Déduit l'identifiant de niveau de l'apprenant depuis sa Progression.
     *
     * Logique :
     *  1. On cherche la Progression en cours (startAt renseigné, completedAt NULL)
     *  2. On remonte vers le Module → Chapter → chapter_order
     *  3. chapter_order devient le niveau pour filtrer les Words
     *  4. Fallback : niveau 1 si aucune progression trouvée
     */
    private function resolveLevelFromProgression(User $user, Modules $currentModule): int
    {
        // Cherche la progression active la plus récente pour cet user
        // (completedAt IS NULL = en cours)
        $sql = '
            SELECT c.chapter_order
            FROM progression p
            INNER JOIN modules m  ON m.id  = p.module_id_id
            INNER JOIN chapters c ON c.id  = m.chapter_id_id
            WHERE p.user_id_id     = :userId
              AND p.completed_at IS NULL
            ORDER BY p.start_at DESC
            LIMIT 1
        ';

        $result = $this->em->getConnection()->fetchOne($sql, [
            'userId' => $user->getId(),
        ]);

        // Si une progression active existe, on utilise son chapter_order comme niveau
        // Sinon on part du chapter_order du module courant, ou niveau 1 par défaut
        if ($result !== false && $result !== null) {
            return (int) $result;
        }

        // Fallback : chapter_order du module demandé, ou 1 si inconnu
        $fallback = $this->em->getConnection()->fetchOne('
            SELECT c.chapter_order
            FROM modules m
            INNER JOIN chapters c ON c.id = m.chapter_id_id
            WHERE m.id = :moduleId
        ', ['moduleId' => $currentModule->getId()]);

        return $fallback ? (int) $fallback : 1;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Requêtes SQL natives
    // ─────────────────────────────────────────────────────────────────────────

    private function moduleHasPhraseContent(Modules $module): bool
    {
        $sql = '
            SELECT COUNT(ms.id)
            FROM module_subjects ms
            WHERE ms.module_id_id = :moduleId
        ';

        $count = (int) $this->em->getConnection()
            ->fetchOne($sql, ['moduleId' => $module->getId()]);

        return $count > 0;
    }

    private function fetchGraphemeForModule(Modules $module): ?array
    {
        $sql = '
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
            LIMIT 1
        ';

        $result = $this->em->getConnection()->fetchAssociative($sql, [
            'moduleId' => $module->getId(),
        ]);

        return $result ?: null;
    }

    private function fetchWordsForLevel(int $levelId, int $limit = 5): array
    {
        $sql = '
            SELECT
                w.id,
                w.word,
                w.segmentation,
                w.phonetic_list,
                w.is_syllable,
                w.is_sight_word,
                af.storage_key AS audio_key,
                af.duration_ms AS audio_duration_ms,
                l.name         AS level_name
            FROM words w
            LEFT JOIN audio_files af ON af.id  = w.audio_path_id
            LEFT JOIN levels l       ON l.id   = w.level_id_id
            WHERE w.level_id_id = :levelId
            ORDER BY w.id ASC
            LIMIT :limit
        ';

        $words = $this->em->getConnection()->fetchAllAssociative($sql, [
            'levelId' => $levelId,
            'limit'   => $limit,
        ]);

        // Pas assez de mots pour ce niveau → fallback tous niveaux confondus
        if (count($words) < $limit) {
            $words = $this->em->getConnection()->fetchAllAssociative('
                SELECT
                    w.id,
                    w.word,
                    w.segmentation,
                    w.phonetic_list,
                    w.is_syllable,
                    w.is_sight_word,
                    af.storage_key AS audio_key,
                    af.duration_ms AS audio_duration_ms,
                    l.name         AS level_name
                FROM words w
                LEFT JOIN audio_files af ON af.id = w.audio_path_id
                LEFT JOIN levels l       ON l.id  = w.level_id_id
                ORDER BY w.level_id_id ASC, w.id ASC
                LIMIT :limit
            ', ['limit' => $limit]);
        }

        return $words;
    }

    private function fetchSubjectsForModule(Modules $module): array
    {
        $sql = '
            SELECT
                s.id,
                s.name,
                s.phonotique_list,
                af.storage_key AS audio_key
            FROM module_subjects ms
            INNER JOIN subjects s   ON s.id  = ms.subject_id_id
            LEFT  JOIN audio_files af ON af.id = s.audio_path_id
            WHERE ms.module_id_id = :moduleId
        ';

        return $this->em->getConnection()->fetchAllAssociative($sql, [
            'moduleId' => $module->getId(),
        ]);
    }

    private function fetchVerbsForModule(Modules $module): array
    {
        $sql = '
            SELECT
                v.id,
                v.name,
                v.phonotique_list,
                af.storage_key AS audio_key
            FROM module_verbs mv
            INNER JOIN verbs v       ON v.id  = mv.verb_id_id
            LEFT  JOIN audio_files af ON af.id = v.audio_path_id_id
            WHERE mv.module_id_id = :moduleId
        ';

        return $this->em->getConnection()->fetchAllAssociative($sql, [
            'moduleId' => $module->getId(),
        ]);
    }

    private function fetchComplementsForModule(Modules $module): array
    {
        $sql = '
            SELECT
                c.id,
                c.name,
                c.phonotique_list,
                af.storage_key AS audio_key
            FROM module_complements mc
            INNER JOIN complements c  ON c.id  = mc.complement_id_id
            LEFT  JOIN audio_files af ON af.id = c.audio_path_id
            WHERE mc.module_id_id = :moduleId
        ';

        return $this->em->getConnection()->fetchAllAssociative($sql, [
            'moduleId' => $module->getId(),
        ]);
    }
}