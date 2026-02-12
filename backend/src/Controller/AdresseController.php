<?php

namespace App\Controller;

use App\Entity\Adresse;
use DateTimeImmutable; // Corrigé
use App\Repository\AdresseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

#[Route('/api/adresse', name: 'app_api_adresse_')]
#[OA\Tag(name: 'Adresses')]
class AdresseController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private AdresseRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}
   
    #[Route('', name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/adresse',
        summary: 'Créer une nouvelle adresse',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'street', type: 'string', example: '12 rue de la Paix'),
                    new OA\Property(property: 'city', type: 'string', example: 'Paris'),
                    new OA\Property(property: 'zipCode', type: 'string', example: '75000')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Adresse créée avec succès')
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        $adresse = $this->serializer->deserialize($request->getContent(), Adresse::class, 'json');
        $adresse->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($adresse);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($adresse, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_adresse_show',
            ['id' => $adresse->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/adresse/{id}',
        summary: 'Afficher une adresse par son ID',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Détails de l\'adresse'),
            new OA\Response(response: 404, description: 'Adresse non trouvée')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $adresse = $this->repository->findOneBy(['id' => $id]);
        if ($adresse) {
            $responseData = $this->serializer->serialize($adresse, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/adresse/{id}',
        summary: 'Modifier une adresse existante',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Adresse mise à jour'),
            new OA\Response(response: 404, description: 'Adresse non trouvée')
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $adresse = $this->repository->findOneBy(['id' => $id]);
        if ($adresse) {
            $this->serializer->deserialize(
                $request->getContent(),
                Adresse::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $adresse]
            );
            $adresse->setUpdatedAt(new DateTimeImmutable());
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/adresse/{id}',
        summary: 'Supprimer une adresse',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Adresse supprimée')
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $adresse = $this->repository->findOneBy(['id' => $id]);
        if ($adresse) {
            $this->manager->remove($adresse);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}