<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;


class RegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        // 1. Récupérer les données envoyées par Postman
        $data = json_decode($request->getContent(), true);

        // 2. Vérifier que les champs requis sont là
        if (empty($data['telephone']) || empty($data['password'])) {
            return new JsonResponse(['error' => 'Téléphone et mot de passe requis'], 400);
        }

        // 3. Créer le nouvel utilisateur
        $user = new User();
        $user->setTelephone($data['telephone']);
        
        // 4. Hacher le mot de passe avant de le sauvegarder
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // (Optionnel) Tu peux définir des rôles par défaut ici
        // $user->setRoles(['ROLE_USER']);

        // 5. Sauvegarder en base de données
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Utilisateur créé avec succès'], 201);
    }
}