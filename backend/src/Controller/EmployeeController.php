<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/employee', name: 'app_employee_')]
#[IsGranted('ROLE_EMPLOYEE')] // ✅ Toutes les routes requirent ROLE_EMPLOYEE
class EmployeeController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    public function dashboard(): JsonResponse
    {
        return new JsonResponse(['message' => 'Bienvenue sur le dashboard employé']);
    }

    #[Route('/orders', name: 'list_orders', methods: ['GET'])]
    public function listOrders(): JsonResponse
    {
        // Voir les commandes
        return new JsonResponse(['message' => 'Liste des commandes']);
    }
}