<?php

// src/Controller/Api/AdminStatsController.php
namespace App\Controller\Api;

use App\Document\MenuStats;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/admin/stats', name: 'api_admin_stats_')]
final class AdminStatsController extends AbstractController
{
    #[Route('/menus', name: 'menus', methods: ['GET'])]
    public function getMenuStats(DocumentManager $dm, Request $request): JsonResponse
    {
        $start = $request->query->get('start'); // Format YYYY-MM-DD
        $end = $request->query->get('end');

        $builder = $dm->createAggregationBuilder(MenuStats::class);

        // 1. Filtre temporel (Match)
        if ($start && $end) {
            $builder->match()
                ->field('createdAt')
                ->gte(new \DateTimeImmutable($start . ' 00:00:00'))
                ->lte(new \DateTimeImmutable($end . ' 23:59:59'));
        }

        // 2. Groupement et calculs (Group)
        $builder
            ->group()
                ->field('id')->expression('$menuName')
                ->field('nombreCommandes')->sum(1)
                ->field('caTotal')->sum('$price')
            ->sort(['caTotal' => -1]); // On trie par CA décroissant

        $results = $builder->getAggregation()->getIterator()->toArray();

        return $this->json($results);
    }
}