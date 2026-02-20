<?php

namespace App\Controller;

use App\Entity\Dish;
use DateTimeImmutable;
use App\Repository\DishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

#[Route('/api/dish', name: 'app_api_dish_')]
#[OA\Tag(name: 'Dishes')]
class DishController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private DishRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}
   
    #[Route('', name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/dish',
        summary: 'Créer un nouveau plat',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Ratatouille'),
                    new OA\Property(property: 'description', type: 'string', example: 'Légumes du soleil mijotés'),
                    new OA\Property(property: 'price', type: 'number', example: 14.50)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Plat créé avec succès')
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        $dish = $this->serializer->deserialize($request->getContent(), Dish::class, 'json');
        $dish->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($dish);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($dish, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_dish_show',
            ['id' => $dish->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/dish/{id}',
        summary: 'Afficher les détails d\'un plat',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Succès'),
            new OA\Response(response: 404, description: 'Plat non trouvé')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $dish = $this->repository->findOneBy(['id' => $id]);
        if ($dish) {
            $responseData = $this->serializer->serialize($dish, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/dish/{id}',
        summary: 'Modifier un plat existant',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
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
                [AbstractNormalizer::OBJECT_TO_POPULATE => $dish]
            );
            $dish->setUpdatedAt(new DateTimeImmutable());
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/dish/{id}',
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

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}