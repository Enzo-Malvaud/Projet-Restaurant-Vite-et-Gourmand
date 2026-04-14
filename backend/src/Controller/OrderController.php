<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\MenuRepository;
use App\Repository\NoticeRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;

#[Route('/api/orders', name: 'app_api_order_')]
#[OA\Tag(name: 'Orders')]
class OrderController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private OrderRepository $repository,
        private UserRepository $userRepository,
        private NoticeRepository $noticeRepository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    #[Route('', name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/orders',
        summary: 'Créer une nouvelle commande',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Commande anniversaire'),
                    new OA\Property(property: 'delivery_datetime', type: 'string', format: 'date-time', example: '2026-04-15T18:30:00Z'),
                    new OA\Property(property: 'number_of_persons', type: 'integer', example: 4),
                    new OA\Property(property: 'status', type: 'string', example: 'pending'),
                    new OA\Property(property: 'user', type: 'string', example: "019d405a-6333-7052-8a04-52261ae33ad1"),
                    new OA\Property(property: 'notice', type: 'integer', example: 1, nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Commande créée avec succès'),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 404, description: 'User ou Notice introuvable'),
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        // 1. Décodage JSON brut
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['message' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }

        // 2. Désérialisation des champs scalaires (title, delivery_datetime, number_of_persons, status)
        /** @var Order $order */
        $order = $this->serializer->deserialize(
            $request->getContent(),
            Order::class,
            'json',
            [AbstractNormalizer::GROUPS => ['order:write']]
        );

        // 3. Résolution User (obligatoire)
        // APRÈS
        if (!isset($data['user']) || !is_string($data['user'])) {
        return new JsonResponse(['message' => 'Field "user" is required and must be a UUID string'], Response::HTTP_BAD_REQUEST);
        }
        $user = $this->userRepository->find(Uuid::fromString($data['user']));
        if (!$user) {
            return new JsonResponse(['message' => sprintf('User #%d not found', $data['user'])], Response::HTTP_NOT_FOUND);
        }
        $order->setUser($user);

        // 4. Résolution Notice (optionnelle)
        if (isset($data['notice']) && is_int($data['notice'])) {
            $notice = $this->noticeRepository->find($data['notice']);
            if (!$notice) {
                return new JsonResponse(['message' => sprintf('Notice #%d not found', $data['notice'])], Response::HTTP_NOT_FOUND);
            }
            $order->setNotice($notice);
        }

        // 5. Initialisation des prix (les items seront ajoutés via OrderItemController)
        $order->setOrderPrice(0.0);
        $order->setDeliveryPrice(5.00);
        $order->setTotalPrice(5.00);

        // 6. Persistance
        $this->manager->persist($order);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($order, 'json', [
            AbstractNormalizer::GROUPS => ['order:read']
        ]);

        $location = $this->urlGenerator->generate(
            'app_api_order_show',
            ['id' => $order->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/orders/{id}',
        summary: 'Récupérer les détails d\'une commande',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Détails de la commande'),
            new OA\Response(response: 404, description: 'Commande non trouvée')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $order = $this->repository->findOneBy(['id' => $id]);
        if ($order) {
            $responseData = $this->serializer->serialize($order, 'json', [
                AbstractNormalizer::GROUPS => ['order:read']
            ]);
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/orders/{id}',
        summary: 'Modifier une commande existante',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'status', type: 'string', example: 'confirmed'),
                    new OA\Property(property: 'delivery_datetime', type: 'string', format: 'date-time')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Commande mise à jour'),
            new OA\Response(response: 404, description: 'Commande non trouvée')
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
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $order,
                    AbstractNormalizer::GROUPS => ['order:write']
                ]
            );
            $this->manager->flush();

            return new JsonResponse(['message' => 'Order updated'], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/orders/{id}',
        summary: 'Supprimer une commande',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Commande supprimée'),
            new OA\Response(response: 404, description: 'Commande non trouvée')
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

        return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/orders',
        summary: 'Lister toutes les commandes',
        responses: [
            new OA\Response(response: 200, description: 'Liste des commandes')
        ]
    )]
    public function list(): JsonResponse
    {
        $orders = $this->repository->findAll();

        $responseData = $this->serializer->serialize($orders, 'json', [
            AbstractNormalizer::GROUPS => ['order:read']
        ]);

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }
}