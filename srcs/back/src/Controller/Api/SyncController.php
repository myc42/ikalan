<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\Sync\SyncIngestionService;
use App\Service\Sync\SyncRequestValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api', name: 'api_')]
class SyncController extends AbstractController
{
    public function __construct(
        private readonly SyncRequestValidator  $validator,
        private readonly SyncIngestionService  $ingestionService,
    ) {}

    /**
     * POST /api/sync
     *
     * Reçoit le carnet de bord du téléphone après une session.
     * Traitement synchrone pour l'instant (asynchrone via Messenger plus tard).
     *
     * Body JSON attendu :
     * {
     *   "lesson_id":    "lesson_abc123",
     *   "module_id":    1,
     *   "completed_at": "2025-06-25T14:30:00+00:00",
     *   "events": [
     *     {
     *       "exercise_id":      "ex-g-1",
     *       "item_type":        "grapheme",
     *       "item_id":          1,
     *       "mode":             "discovery",
     *       "source_module_id": 1,
     *       "response_time_ms": 1200,
     *       "attempts":         1,
     *       "success":          true
     *     }
     *   ]
     * }
     */
    #[Route('/sync', name: 'sync', methods: ['POST'])]
    public function sync(
        Request $request,
        #[CurrentUser] ?User $user,
    ): JsonResponse {
        // ── 1. Authentification ───────────────────────────────────────────────
        if ($user === null) {
            return $this->json([
                'error' => 'Non authentifié.',
            ], 401);
        }

        // ── 2. Décodage du JSON ───────────────────────────────────────────────
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return $this->json([
                'error' => 'Le corps de la requête doit être un JSON valide.',
                'hint'  => 'Vérifie que Content-Type: application/json est bien présent.',
            ], 400);
        }

        // ── 3. Validation du payload ──────────────────────────────────────────
        try {
            $payload = $this->validator->validate($data);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'error'  => 'Payload invalide.',
                'detail' => $e->getMessage(),
            ], 422);
        }

        // ── 4. Traitement ─────────────────────────────────────────────────────
        try {
            $result = $this->ingestionService->process($user, $payload);
        } catch (\Exception $e) {
            return $this->json([
                'error'  => 'Erreur lors du traitement de la synchronisation.',
                'detail' => $e->getMessage(),
            ], 500);
        }

        // ── 5. Réponse ────────────────────────────────────────────────────────
        return $this->json([
            'status'  => 'ok',
            'message' => 'Synchronisation traitée avec succès.',
            'result'  => $result,
        ], 200);
    }
}