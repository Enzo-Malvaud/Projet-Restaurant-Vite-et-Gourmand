<?php

namespace App\Controller;

use App\Entity\Rental;
use DateTimeImmutable;
use App\Repository\RentalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA; // Indispensable pour la doc

#[Route('/api/rental', name: 'app_api_rental_')]
#[OA\Tag(name: 'Rentals')]
class RentalController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private RentalRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}
   
    #[Route('', name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/rental',
        summary: 'Créer une nouvelle location',
        description: 'Permet de créer une entrée de location en base de données.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Appartement bord de mer'),
                    new OA\Property(property: 'price', type: 'number', example: 450.00)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Location créée avec succès')
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        $rental = $this->serializer->deserialize($request->getContent(), Rental::class, 'json');
        $rental->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($rental);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($rental, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_rental_show',
            ['id' => $rental->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/rental/{id}',
        summary: 'Afficher les détails d\'une location',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Détails de la location'),
            new OA\Response(response: 404, description: 'Location non trouvée')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $rental = $this->repository->findOneBy(['id' => $id]);
        if ($rental) {
            $responseData = $this->serializer->serialize($rental, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/rental/{id}',
        summary: 'Modifier une location existante',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Location mise à jour'),
            new OA\Response(response: 404, description: 'Location non trouvée')
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $rental = $this->repository->findOneBy(['id' => $id]);
        if ($rental) {
            $this->serializer->deserialize(
                $request->getContent(),
                Rental::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $rental]
            );
            $rental->setUpdatedAt(new DateTimeImmutable());
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/rental/{id}',
        summary: 'Supprimer une location',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Location supprimée'),
            new OA\Response(response: 404, description: 'Location non trouvée')
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $rental = $this->repository->findOneBy(['id' => $id]);
        if ($rental) {
            $this->manager->remove($rental);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}