<?php

namespace App\Controller;

use App\Entity\Regime;
use DateTimeImmutable; // Corrigé (T majuscule)
use App\Repository\RegimeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

#[Route('/api/regime', name: 'app_api_regime_')]
#[OA\Tag(name: 'Regimes')]
class RegimeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private RegimeRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}
   
    #[Route('', name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/regime',
        summary: 'Créer un nouveau régime',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Végétarien')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Régime créé')
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        $regime = $this->serializer->deserialize($request->getContent(), Regime::class, 'json');
        $regime->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($regime);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($regime, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_regime_show',
            ['id' => $regime->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/regime/{id}',
        summary: 'Afficher un régime spécifique',
        parameters: [new OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Succès'),
            new OA\Response(response: 404, description: 'Non trouvé')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $regime = $this->repository->findOneBy(['id' => $id]);
        if ($regime) {
            $responseData = $this->serializer->serialize($regime, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/regime/{id}',
        summary: 'Modifier un régime',
        parameters: [new OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Mis à jour')
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $regime = $this->repository->findOneBy(['id' => $id]);
        if ($regime) {
            $this->serializer->deserialize(
                $request->getContent(),
                Regime::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $regime]
            );
            $regime->setUpdatedAt(new DateTimeImmutable());
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/regime/{id}',
        summary: 'Supprimer un régime',
        parameters: [new OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Supprimé')
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $regime = $this->repository->findOneBy(['id' => $id]);
        if ($regime) {
            $this->manager->remove($regime); // Corrigé : on utilise le manager
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}