<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/admin', name: 'app_admin_')]
#[IsGranted('ROLE_ADMIN')] // ✅ Toutes les routes requirent ROLE_ADMIN
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    public function dashboard(): JsonResponse
    {
        return new JsonResponse(['message' => 'Bienvenue sur le dashboard admin']);
    }

    #[Route('/users', name: 'list_users', methods: ['GET'])]
    public function listUsers(): JsonResponse
    {
        // Gérer les utilisateurs
        return new JsonResponse(['message' => 'Liste des utilisateurs']);
    }

    #[Route('/users/{id}/role', name: 'update_user_role', methods: ['PUT'])]
    public function updateUserRole(string $id): JsonResponse
    {
        // Promouvoir/rétrograder un utilisateur
        return new JsonResponse(['message' => 'Rôle mis à jour']);
    }
}