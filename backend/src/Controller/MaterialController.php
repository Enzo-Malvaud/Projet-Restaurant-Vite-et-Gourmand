<?php

namespace App\Controller;

use App\Entity\Material;
use DateTimeImmutable; // Corrigé
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

#[Route('/api/material', name: 'app_api_material_')]
#[OA\Tag(name: 'Materials')]
class MaterialController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private MaterialRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}
   
    #[Route('', name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/material',
        summary: 'Ajouter un nouveau matériel',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Perceuse à percussion'),
                    new OA\Property(property: 'quantity', type: 'integer', example: 5)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Matériel créé')
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        $material = $this->serializer->deserialize($request->getContent(), Material::class, 'json');
        $material->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($material);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($material, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_material_show',
            ['id' => $material->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/material/{id}',
        summary: 'Voir les détails d\'un matériel',
        parameters: [new OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Succès'),
            new OA\Response(response: 404, description: 'Non trouvé')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $material = $this->repository->findOneBy(['id' => $id]);
        if ($material) {
            $responseData = $this->serializer->serialize($material, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/material/{id}',
        summary: 'Modifier un matériel existant',
        parameters: [new OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Mis à jour')
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $material = $this->repository->findOneBy(['id' => $id]);
        if ($material) {
            $this->serializer->deserialize(
                $request->getContent(),
                Material::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $material]
            );
            $material->setUpdatedAt(new DateTimeImmutable());
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/material/{id}',
        summary: 'Supprimer un matériel',
        parameters: [new OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Supprimé')
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

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}