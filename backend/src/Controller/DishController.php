<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Repository\DishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;


#[Route('/api/dishes', name: 'app_api_dish_')]
#[OA\Tag(name: 'Dishes')]
class DishController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private DishRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}


    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/dishes',
        summary: 'Lister tous les plats',
        responses: [
            new OA\Response(response: 200, description: 'Liste des plats')
        ]
    )]
    public function list(): JsonResponse
    {
        $dishes = $this->repository->findAll();

        $responseData = $this->serializer->serialize($dishes, 'json', [
            AbstractNormalizer::GROUPS => ['dish:read']
        ]);

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }
   
    #[Route('', name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/dishes',
        summary: 'Créer un nouveau plat',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'dish_title', type: 'string', example: 'Ratatouille'),
                    new OA\Property(property: 'description', type: 'string', example: 'Légumes du soleil mijotés'),
                    new OA\Property(property: 'price', type: 'number', example: 14.50),
                    new OA\Property(property: 'type_of_dish', type: 'string', example: 'Accompagnement'),
                    new OA\Property(property: 'allergens', type: 'string', example: 'Aucun'),
                    new OA\Property(property: 'picture', type: 'string', example: 'ratatouille.jpg')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Plat créé avec succès'),
            new OA\Response(response: 400, description: 'Données invalides')
        ]
    )]
    public function new(Request $request): JsonResponse
    {

        $dish = $this->serializer->deserialize($request->getContent(), Dish::class, 'json', [
            AbstractNormalizer::GROUPS => ['dish:write']
        ]);


        $this->manager->persist($dish);
        $this->manager->flush();


        $responseData = $this->serializer->serialize($dish, 'json', [
            AbstractNormalizer::GROUPS => ['dish:read']
        ]);
        $location = $this->urlGenerator->generate(
            'app_api_dish_show',
            ['id' => $dish->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/dishes/{id}',
        summary: 'Afficher les détails d\'un plat',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Détails du plat'),
            new OA\Response(response: 404, description: 'Plat non trouvé')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $dish = $this->repository->findOneBy(['id' => $id]);
        if ($dish) {

            $responseData = $this->serializer->serialize($dish, 'json', [
                AbstractNormalizer::GROUPS => ['dish:read']
            ]);
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(['message' => 'Dish not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/dishes/{id}',
        summary: 'Modifier un plat existant',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'dish_title', type: 'string', example: 'Nouveau nom'),
                    new OA\Property(property: 'price', type: 'number', example: 16.50)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Plat mis à jour'),
            new OA\Response(response: 404, description: 'Plat non trouvé')
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $dish = $this->repository->findOneBy(['id' => $id]);
        if ($dish) {

            $this->serializer->deserialize(
                $request->getContent(),
                Dish::class,
                'json',
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $dish,
                    AbstractNormalizer::GROUPS => ['dish:write']
                ]
            );
            $this->manager->flush();

            return new JsonResponse(['message' => 'Dish updated'], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Dish not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/dishes/{id}',
        summary: 'Supprimer un plat',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Plat supprimé'),
            new OA\Response(response: 404, description: 'Plat non trouvé')
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $dish = $this->repository->findOneBy(['id' => $id]);
        if ($dish) {
            $this->manager->remove($dish);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(['message' => 'Dish not found'], Response::HTTP_NOT_FOUND);
    }
}