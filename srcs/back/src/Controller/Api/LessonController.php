<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\ModulesRepository;
use App\Repository\UserModuleProgressRepository;
use App\Service\Lesson\LessonComposer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', name: 'api_')]
class LessonController extends AbstractController
{
    public function __construct(
        private readonly ModulesRepository            $modulesRepo,
        private readonly UserModuleProgressRepository $progressRepo,
        private readonly LessonComposer               $composer,
    ) {}

    /**
     * GET /api/lesson/next
     *
     * Résolution du module dans l'ordre de priorité :
     *  1. X-Module-Id forcé (mode test Postman)
     *  2. Module en fenêtre SRS ouverte dans user_module_progress (révision urgente)
     *  3. Module en cours dans Progression (completedAt IS NULL)
     *  4. Premier module jamais commencé selon chapter_order + module_order
     */
    #[Route('/lesson/next', name: 'lesson_next', methods: ['GET'])]
    public function next(
        Request $request,
        #[CurrentUser] ?User $user,
    ): JsonResponse {
        // ── 1. Authentification ───────────────────────────────────────────────
        if ($user === null) {
            return $this->json([
                'error' => 'Non authentifié.',
                'hint'  => 'Ajoute "Authorization: Bearer <token>" dans tes headers.',
            ], 401);
        }

        // ── 2. Sélection du module ─────────────────────────────────────────────
        $forcedModuleId = $request->headers->get('X-Module-Id');

        if ($forcedModuleId !== null) {
            // Mode test : module forcé via header
            $module = $this->modulesRepo->find((int) $forcedModuleId);

            if ($module === null) {
                return $this->json([
                    'error' => "Module {$forcedModuleId} introuvable.",
                ], 404);
            }
        } else {
            $module = $this->resolveNextModule($user);

            if ($module === null) {
                return $this->json([
                    'message' => 'Aucun module disponible pour le moment.',
                    'hint'    => 'Tous les modules sont maîtrisés ou aucune fenêtre SRS n\'est ouverte.',
                ], 200);
            }
        }

        // ── 3. Composition de la leçon ────────────────────────────────────────
        $payload = $this->composer->compose($user, $module);

        return $this->json($payload);
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function resolveNextModule(User $user): ?\App\Entity\Modules
    {
        // Priorité 1 : révision SRS urgente (fenêtre ouverte aujourd'hui)
        $progressInWindow = $this->progressRepo->findOneInWindow($user);
        if ($progressInWindow !== null) {
            return $progressInWindow->getModuleId();
        }

        // Priorité 2 : module en cours dans Progression,
        //              ou premier module jamais commencé
        return $this->modulesRepo->findNextForUser($user);
    }
}