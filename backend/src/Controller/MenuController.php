<?php

namespace App\Controller;

use App\Entity\Menu;
use DateTimeImmutable; // Corrigé
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

#[Route('/api/menu', name: 'app_api_menu_')]
#[OA\Tag(name: 'Menus')]
class MenuController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private MenuRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}
   
    #[Route('', name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/menu',
        summary: 'Créer un nouveau menu',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Menu Dégustation'),
                    new OA\Property(property: 'price', type: 'number', example: 75.50)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Menu créé avec succès')
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        $menu = $this->serializer->deserialize($request->getContent(), Menu::class, 'json');
        $menu->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($menu);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($menu, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_menu_show',
            ['id' => $menu->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/menu/{id}',
        summary: 'Récupérer un menu par son ID',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Détails du menu'),
            new OA\Response(response: 404, description: 'Menu non trouvé')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $menu = $this->repository->findOneBy(['id' => $id]);
        if ($menu) {
            $responseData = $this->serializer->serialize($menu, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/menu/{id}',
        summary: 'Modifier un menu existant',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Menu mis à jour'),
            new OA\Response(response: 404, description: 'Menu non trouvé')
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $menu = $this->repository->findOneBy(['id' => $id]);
        if ($menu) {
            $this->serializer->deserialize(
                $request->getContent(),
                Menu::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $menu]
            );
            $menu->setUpdatedAt(new DateTimeImmutable());
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/menu/{id}',
        summary: 'Supprimer un menu',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Menu supprimé'),
            new OA\Response(response: 404, description: 'Menu non trouvé')
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $menu = $this->repository->findOneBy(['id' => $id]);
        if ($menu) {
            $this->manager->remove($menu);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}