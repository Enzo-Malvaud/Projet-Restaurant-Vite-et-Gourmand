<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;


#[Route('/api/menus', name: 'app_api_menu_')]
#[OA\Tag(name: 'Menus')]
class MenuController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private MenuRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}


    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/menus',
        summary: 'Lister tous les menus',
        responses: [
            new OA\Response(response: 200, description: 'Liste des menus')
        ]
    )]
    public function list(): JsonResponse
    {
        $menus = $this->repository->findAll();

        $responseData = $this->serializer->serialize($menus, 'json', [
            AbstractNormalizer::GROUPS => ['menu:read']
        ]);

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }
   
    #[Route('', name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/menus',
        summary: 'Créer un nouveau menu',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title_menu', type: 'string', example: 'Menu Dégustation'),
                    new OA\Property(property: 'description', type: 'string', example: 'Menu gastronomique'),
                    new OA\Property(property: 'minimum_number_of_persons', type: 'integer', example: 4),
                    new OA\Property(property: 'price_menu', type: 'string', example: "75.50"),
                    new OA\Property(property: 'remaining_quantity', type: 'integer', example: 10),
                    new OA\Property(property: 'precaution_menu', type: 'string', example: 'Peut contenir des arachides'),
                    new OA\Property(property: 'storage_precautions', type: 'string', example: 'Conserver à 4°C')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Menu créé avec succès'),
            new OA\Response(response: 400, description: 'Données invalides')
        ]
    )]
    public function new(Request $request): JsonResponse
    {

        $menu = $this->serializer->deserialize($request->getContent(), Menu::class, 'json', [
            AbstractNormalizer::GROUPS => ['menu:write']
        ]);

        $this->manager->persist($menu);
        $this->manager->flush();


        $responseData = $this->serializer->serialize($menu, 'json', [
            AbstractNormalizer::GROUPS => ['menu:read']
        ]);
        $location = $this->urlGenerator->generate(
            'app_api_menu_show',
            ['id' => $menu->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/menus/{id}',
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

            $responseData = $this->serializer->serialize($menu, 'json', [
                AbstractNormalizer::GROUPS => ['menu:read']
            ]);
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(['message' => 'Menu not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/menus/{id}',
        summary: 'Modifier un menu existant',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title_menu', type: 'string', example: 'Nouveau titre'),
                    new OA\Property(property: 'price_menu', type: 'number', example: 85.00)
                ]
            )
        ),
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
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $menu,
                    AbstractNormalizer::GROUPS => ['menu:write']
                ]
            );
            $this->manager->flush();

            return new JsonResponse(['message' => 'Menu updated'], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Menu not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/menus/{id}',
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

        return new JsonResponse(['message' => 'Menu not found'], Response::HTTP_NOT_FOUND);
    }
}