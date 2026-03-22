<?php

namespace App\Controller;

use App\Entity\Material;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

/**
 * ✅ CORRIGÉ: Route au pluriel (/api/materials)
 */
#[Route('/api/materials', name: 'app_api_material_')]
#[OA\Tag(name: 'Materials')]
class MaterialController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private MaterialRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    /**
     * ✅ AJOUT: Lister tous les matériaux
     */
    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/materials',
        summary: 'Lister tous les matériaux',
        responses: [
            new OA\Response(response: 200, description: 'Liste des matériaux')
        ]
    )]
    public function list(): JsonResponse
    {
        $materials = $this->repository->findAll();

        $responseData = $this->serializer->serialize($materials, 'json', [
            AbstractNormalizer::GROUPS => ['material:read']
        ]);

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }
   
    #[Route('', name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/materials',
        summary: 'Ajouter un nouveau matériel',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Perceuse à percussion'),
                    new OA\Property(property: 'description', type: 'string', example: 'Perceuse professionnelle'),
                    new OA\Property(property: 'daily_rental_price', type: 'number', example: 25.50),
                    new OA\Property(property: 'total_quantity', type: 'integer', example: 5),
                    new OA\Property(property: 'caution', type: 'number', example: 100.00),
                    new OA\Property(property: 'picture', type: 'string', example: 'drill.jpg'),
                    new OA\Property(property: 'rental_condition', type: 'string', example: 'Bon état')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Matériel créé avec succès'),
            new OA\Response(response: 400, description: 'Données invalides')
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        /**
         * ✅ CORRIGÉ: Ajout des groupes de sérialisation
         */
        $material = $this->serializer->deserialize($request->getContent(), Material::class, 'json', [
            AbstractNormalizer::GROUPS => ['material:write']
        ]);

        /**
         * ✅ CORRIGÉ: Suppression du setCreatedAt() - PrePersist le fait
         */
        $this->manager->persist($material);
        $this->manager->flush();

        /**
         * ✅ CORRIGÉ: Ajout des groupes de sérialisation
         */
        $responseData = $this->serializer->serialize($material, 'json', [
            AbstractNormalizer::GROUPS => ['material:read']
        ]);
        $location = $this->urlGenerator->generate(
            'app_api_material_show',
            ['id' => $material->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/materials/{id}',
        summary: 'Voir les détails d\'un matériel',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Détails du matériel'),
            new OA\Response(response: 404, description: 'Matériel non trouvé')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $material = $this->repository->findOneBy(['id' => $id]);
        if ($material) {
            /**
             * ✅ CORRIGÉ: Ajout des groupes de sérialisation
             */
            $responseData = $this->serializer->serialize($material, 'json', [
                AbstractNormalizer::GROUPS => ['material:read']
            ]);
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(['message' => 'Material not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/materials/{id}',
        summary: 'Modifier un matériel existant',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Nouvelle perceuse'),
                    new OA\Property(property: 'daily_rental_price', type: 'number', example: 30.00)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Matériel mis à jour'),
            new OA\Response(response: 404, description: 'Matériel non trouvé')
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $material = $this->repository->findOneBy(['id' => $id]);
        if ($material) {
            /**
             * ✅ CORRIGÉ: Ajout des groupes de sérialisation
             * + Suppression du setUpdatedAt() - PreUpdate le fera
             */
            $this->serializer->deserialize(
                $request->getContent(),
                Material::class,
                'json',
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $material,
                    AbstractNormalizer::GROUPS => ['material:write']
                ]
            );
            $this->manager->flush();

            return new JsonResponse(['message' => 'Material updated'], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Material not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/materials/{id}',
        summary: 'Supprimer un matériel',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Matériel supprimé'),
            new OA\Response(response: 404, description: 'Matériel non trouvé')
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $material = $this->repository->findOneBy(['id' => $id]);
        if ($material) {
            $this->manager->remove($material);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(['message' => 'Material not found'], Response::HTTP_NOT_FOUND);
    }
}