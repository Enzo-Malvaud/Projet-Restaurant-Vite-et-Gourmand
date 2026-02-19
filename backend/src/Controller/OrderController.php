<?php

namespace App\Controller;

use App\Entity\Order;
use DateTimeImmutable; // Corrigé
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

#[Route('/api/order', name: 'app_api_order_')]
#[OA\Tag(name: 'Orders')]
class OrderController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private OrderRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}
   
    #[Route('', name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/order',
        summary: 'Créer une nouvelle commande',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'reference', type: 'string', example: 'CMD-2026-001'),
                    new OA\Property(property: 'totalPrice', type: 'number', example: 99.99)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Commande créée avec succès')
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        $order = $this->serializer->deserialize($request->getContent(), Order::class, 'json');
        $order->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($order);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($order, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_order_show',
            ['id' => $order->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/order/{id}',
        summary: 'Récupérer les détails d\'une commande',
        parameters: [new OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Détails de la commande'),
            new OA\Response(response: 404, description: 'Commande non trouvée')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $order = $this->repository->findOneBy(['id' => $id]);
        if ($order) {
            $responseData = $this->serializer->serialize($order, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/order/{id}',
        summary: 'Modifier une commande existante',
        parameters: [new OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Commande mise à jour')
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $order = $this->repository->findOneBy(['id' => $id]);
        if ($order) {
            $this->serializer->deserialize(
                $request->getContent(),
                Order::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $order]
            );
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/order/{id}',
        summary: 'Supprimer une commande',
        parameters: [new OA\Parameter(name: 'id', in: 'path', schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Commande supprimée')
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $order = $this->repository->findOneBy(['id' => $id]);
        if ($order) {
            $this->manager->remove($order);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}