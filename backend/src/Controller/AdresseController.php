<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Repository\AdresseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;


#[Route('/api/adresses', name: 'app_api_adresse_')]
#[OA\Tag(name: 'Adresses')]
class AdresseController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private AdresseRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}


    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/adresses',
        summary: 'Lister toutes les adresses',
        responses: [
            new OA\Response(response: 200, description: 'Liste des adresses')
        ]
    )]
    public function list(): JsonResponse
    {
        $adresses = $this->repository->findAll();

        $responseData = $this->serializer->serialize($adresses, 'json', [
            AbstractNormalizer::GROUPS => ['adresse:read']
        ]);

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }
   
    #[Route('', name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/adresses',
        summary: 'Créer une nouvelle adresse',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'adresse', type: 'string', example: '12 rue de la Paix'),
                    new OA\Property(property: 'city', type: 'string', example: 'Paris'),
                    new OA\Property(property: 'postalCode', type: 'string', example: '75000'),
                    new OA\Property(property: 'country', type: 'string', example: 'France')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Adresse créée avec succès'),
            new OA\Response(response: 400, description: 'Données invalides')
        ]
    )]
    public function new(Request $request): JsonResponse
    {
 
        $adresse = $this->serializer->deserialize($request->getContent(), Adresse::class, 'json', [
            AbstractNormalizer::GROUPS => ['adresse:write']
        ]);

    
        $this->manager->persist($adresse);
        $this->manager->flush();

     
        $responseData = $this->serializer->serialize($adresse, 'json', [
            AbstractNormalizer::GROUPS => ['adresse:read']
        ]);
        $location = $this->urlGenerator->generate(
            'app_api_adresse_show',
            ['id' => $adresse->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/adresses/{id}',
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
    
            $responseData = $this->serializer->serialize($adresse, 'json', [
                AbstractNormalizer::GROUPS => ['adresse:read']
            ]);
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(['message' => 'Adresse not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/adresses/{id}',
        summary: 'Modifier une adresse existante',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'adresse', type: 'string', example: '15 avenue des Champs'),
                    new OA\Property(property: 'city', type: 'string', example: 'Lyon'),
                    new OA\Property(property: 'postalCode', type: 'string', example: '69000')
                ]
            )
        ),
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
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $adresse,
                    AbstractNormalizer::GROUPS => ['adresse:write']
                ]
            );
            $this->manager->flush();

            return new JsonResponse(['message' => 'Adresse updated'], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Adresse not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/adresses/{id}',
        summary: 'Supprimer une adresse',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Adresse supprimée'),
            new OA\Response(response: 404, description: 'Adresse non trouvée')
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

        return new JsonResponse(['message' => 'Adresse not found'], Response::HTTP_NOT_FOUND);
    }
}