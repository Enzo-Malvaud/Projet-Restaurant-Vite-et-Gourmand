<?php

namespace App\Controller;

use App\Entity\HoraireRestaurant;
use DateTimeImmutable; // Corrigé
use App\Repository\HoraireRestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

#[Route('/api/horaire_restaurant', name: 'app_api_horaire_restaurant_')]
#[OA\Tag(name: 'Horaires Restaurant')]
class HoraireRestaurantController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private HoraireRestaurantRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}
   
    #[Route('', name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/horaire_restaurant',
        summary: 'Créer un nouvel horaire pour le restaurant',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'jour', type: 'string', example: 'Lundi'),
                    new OA\Property(property: 'ouverture', type: 'string', example: '12:00'),
                    new OA\Property(property: 'fermeture', type: 'string', example: '14:30')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Horaire créé avec succès')
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        $horaire_restaurant = $this->serializer->deserialize($request->getContent(), HoraireRestaurant::class, 'json');
        $horaire_restaurant->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($horaire_restaurant);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($horaire_restaurant, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_horaire_restaurant_show',
            ['id' => $horaire_restaurant->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/horaire_restaurant/{id}',
        summary: 'Récupérer un horaire spécifique',
        parameters: [new OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Détails de l\'horaire'),
            new OA\Response(response: 404, description: 'Horaire non trouvé')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $horaire_restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($horaire_restaurant) {
            $responseData = $this->serializer->serialize($horaire_restaurant, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/horaire_restaurant/{id}',
        summary: 'Modifier un horaire existant',
        parameters: [new OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Horaire mis à jour')
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $horaire_restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($horaire_restaurant) {
            $this->serializer->deserialize(
                $request->getContent(),
                HoraireRestaurant::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $horaire_restaurant]
            );
            $horaire_restaurant->setUpdatedAt(new DateTimeImmutable());
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/horaire_restaurant/{id}',
        summary: 'Supprimer un horaire',
        parameters: [new OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Horaire supprimé')
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $horaire_restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($horaire_restaurant) {
            $this->manager->remove($horaire_restaurant);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}