<?php

namespace App\Controller;

use App\Entity\MaterialRental;
use App\Entity\Rental;
use App\Repository\MaterialRentalRepository;
use App\Repository\RentalRepository;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

#[Route('/api/rentals', name: 'app_api_material_rentals_')]
#[OA\Tag(name: 'Material Rentals')]
class MaterialRentalController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private MaterialRentalRepository $repository,
        private RentalRepository $rentalRepository,
        private MaterialRepository $materialRepository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    /**
     * Ajouter un matériel à une location
     */
    #[Route('/{rentalId}/materials', name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/rentals/{rentalId}/materials',
        summary: 'Ajouter un matériel à une location',
        parameters: [new OA\Parameter(name: 'rentalId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'quantity', type: 'integer', example: 2),
                    new OA\Property(property: 'unit_price', type: 'number', example: 25.50),
                    new OA\Property(property: 'material', type: 'integer', example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Matériel ajouté'),
            new OA\Response(response: 404, description: 'Location non trouvée')
        ]
    )]
    public function new(int $rentalId, Request $request): JsonResponse
    {
        $rental = $this->rentalRepository->find($rentalId);
        if (!$rental) {
            return new JsonResponse(['message' => 'Rental not found'], Response::HTTP_NOT_FOUND);
        }

        $materialRental = $this->serializer->deserialize($request->getContent(), MaterialRental::class, 'json', [
            AbstractNormalizer::GROUPS => ['materialRental:write']
        ]);

        // Récupérer le matériel s'il existe
        $data = json_decode($request->getContent(), true);
        if (isset($data['material']) && is_int($data['material'])) {
            $material = $this->materialRepository->find($data['material']);
            if ($material) {
                $materialRental->setMaterial($material);
            }
        }

        $materialRental->setRental($rental);
        $this->manager->persist($materialRental);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($materialRental, 'json', [
            AbstractNormalizer::GROUPS => ['materialRental:read']
        ]);

        $location = $this->urlGenerator->generate(
            'app_api_material_rentals_show',
            ['rentalId' => $rentalId, 'materialId' => $materialRental->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    /**
     * Afficher un matériel d'une location
     */
    #[Route('/{rentalId}/materials/{materialId}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/rentals/{rentalId}/materials/{materialId}',
        summary: 'Afficher un matériel d\'une location',
        parameters: [
            new OA\Parameter(name: 'rentalId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'materialId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Matériel trouvé'),
            new OA\Response(response: 404, description: 'Matériel non trouvé')
        ]
    )]
    public function show(int $rentalId, int $materialId): JsonResponse
    {
        $rental = $this->rentalRepository->find($rentalId);
        if (!$rental) {
            return new JsonResponse(['message' => 'Rental not found'], Response::HTTP_NOT_FOUND);
        }

        $materialRental = $this->repository->findOneBy(['id' => $materialId, 'rental' => $rental]);
        if ($materialRental) {
            $responseData = $this->serializer->serialize($materialRental, 'json', [
                AbstractNormalizer::GROUPS => ['materialRental:read']
            ]);
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(['message' => 'Material rental not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Modifier un matériel d'une location
     */
    #[Route('/{rentalId}/materials/{materialId}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/rentals/{rentalId}/materials/{materialId}',
        summary: 'Modifier un matériel d\'une location',
        parameters: [
            new OA\Parameter(name: 'rentalId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'materialId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'quantity', type: 'integer', example: 3)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Matériel modifié'),
            new OA\Response(response: 404, description: 'Matériel non trouvé')
        ]
    )]
    public function edit(int $rentalId, int $materialId, Request $request): JsonResponse
    {
        $rental = $this->rentalRepository->find($rentalId);
        if (!$rental) {
            return new JsonResponse(['message' => 'Rental not found'], Response::HTTP_NOT_FOUND);
        }

        $materialRental = $this->repository->findOneBy(['id' => $materialId, 'rental' => $rental]);
        if ($materialRental) {
            $this->serializer->deserialize(
                $request->getContent(),
                MaterialRental::class,
                'json',
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $materialRental,
                    AbstractNormalizer::GROUPS => ['materialRental:write']
                ]
            );
            $this->manager->flush();

            return new JsonResponse(['message' => 'Material rental updated'], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Material rental not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Supprimer un matériel d'une location
     */
    #[Route('/{rentalId}/materials/{materialId}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/rentals/{rentalId}/materials/{materialId}',
        summary: 'Supprimer un matériel d\'une location',
        parameters: [
            new OA\Parameter(name: 'rentalId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'materialId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Matériel supprimé'),
            new OA\Response(response: 404, description: 'Matériel non trouvé')
        ]
    )]
    public function delete(int $rentalId, int $materialId): JsonResponse
    {
        $rental = $this->rentalRepository->find($rentalId);
        if (!$rental) {
            return new JsonResponse(['message' => 'Rental not found'], Response::HTTP_NOT_FOUND);
        }

        $materialRental = $this->repository->findOneBy(['id' => $materialId, 'rental' => $rental]);
        if ($materialRental) {
            $this->manager->remove($materialRental);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(['message' => 'Material rental not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Lister tous les matériaux d'une location
     */
    #[Route('/{rentalId}/materials', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/rentals/{rentalId}/materials',
        summary: 'Lister les matériaux d\'une location',
        parameters: [new OA\Parameter(name: 'rentalId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Liste des matériaux'),
            new OA\Response(response: 404, description: 'Location non trouvée')
        ]
    )]
    public function list(int $rentalId): JsonResponse
    {
        $rental = $this->rentalRepository->find($rentalId);
        if (!$rental) {
            return new JsonResponse(['message' => 'Rental not found'], Response::HTTP_NOT_FOUND);
        }

        $materialRentals = $this->repository->findBy(['rental' => $rental]);
        
        $responseData = $this->serializer->serialize($materialRentals, 'json', [
            AbstractNormalizer::GROUPS => ['materialRental:read']
        ]);

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }
}