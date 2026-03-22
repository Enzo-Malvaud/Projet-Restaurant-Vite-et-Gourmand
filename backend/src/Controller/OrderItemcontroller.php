<?php

namespace App\Controller;

use App\Entity\OrderItem;
use App\Entity\Order;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

#[Route('/api/orders', name: 'app_api_order_items_')]
#[OA\Tag(name: 'Order Items')]
class OrderItemController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private OrderItemRepository $repository,
        private OrderRepository $orderRepository,
        private MenuRepository $menuRepository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    /**
     * Ajouter un item à une commande
     */
    #[Route('/{orderId}/items', name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/orders/{orderId}/items',
        summary: 'Ajouter un item à une commande',
        parameters: [new OA\Parameter(name: 'orderId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'quantity', type: 'integer', example: 2),
                    new OA\Property(property: 'price_unit', type: 'number', example: 15.50),
                    new OA\Property(property: 'menu', type: 'integer', example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Item créé'),
            new OA\Response(response: 404, description: 'Commande non trouvée')
        ]
    )]
    public function new(int $orderId, Request $request): JsonResponse
    {
        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        $orderItem = $this->serializer->deserialize($request->getContent(), OrderItem::class, 'json', [
            AbstractNormalizer::GROUPS => ['orderItem:write']
        ]);

        // Récupérer le menu s'il existe
        $data = json_decode($request->getContent(), true);
        if (isset($data['menu']) && is_int($data['menu'])) {
            $menu = $this->menuRepository->find($data['menu']);
            if ($menu) {
                $orderItem->setMenu($menu);
            }
        }

        $orderItem->setOrder($order);
        $this->manager->persist($orderItem);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($orderItem, 'json', [
            AbstractNormalizer::GROUPS => ['orderItem:read']
        ]);

        $location = $this->urlGenerator->generate(
            'app_api_order_items_show',
            ['orderId' => $orderId, 'itemId' => $orderItem->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    /**
     * Afficher un item spécifique
     */
    #[Route('/{orderId}/items/{itemId}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/orders/{orderId}/items/{itemId}',
        summary: 'Afficher un item de commande',
        parameters: [
            new OA\Parameter(name: 'orderId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'itemId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Item trouvé'),
            new OA\Response(response: 404, description: 'Item non trouvé')
        ]
    )]
    public function show(int $orderId, int $itemId): JsonResponse
    {
        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        $orderItem = $this->repository->findOneBy(['id' => $itemId, 'order' => $order]);
        if ($orderItem) {
            $responseData = $this->serializer->serialize($orderItem, 'json', [
                AbstractNormalizer::GROUPS => ['orderItem:read']
            ]);
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(['message' => 'Order item not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Modifier un item
     */
    #[Route('/{orderId}/items/{itemId}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/orders/{orderId}/items/{itemId}',
        summary: 'Modifier un item de commande',
        parameters: [
            new OA\Parameter(name: 'orderId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'itemId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
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
            new OA\Response(response: 200, description: 'Item modifié'),
            new OA\Response(response: 404, description: 'Item non trouvé')
        ]
    )]
    public function edit(int $orderId, int $itemId, Request $request): JsonResponse
    {
        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        $orderItem = $this->repository->findOneBy(['id' => $itemId, 'order' => $order]);
        if ($orderItem) {
            $this->serializer->deserialize(
                $request->getContent(),
                OrderItem::class,
                'json',
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $orderItem,
                    AbstractNormalizer::GROUPS => ['orderItem:write']
                ]
            );
            $this->manager->flush();

            return new JsonResponse(['message' => 'Order item updated'], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Order item not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Supprimer un item
     */
    #[Route('/{orderId}/items/{itemId}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/orders/{orderId}/items/{itemId}',
        summary: 'Supprimer un item de commande',
        parameters: [
            new OA\Parameter(name: 'orderId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'itemId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Item supprimé'),
            new OA\Response(response: 404, description: 'Item non trouvé')
        ]
    )]
    public function delete(int $orderId, int $itemId): JsonResponse
    {
        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        $orderItem = $this->repository->findOneBy(['id' => $itemId, 'order' => $order]);
        if ($orderItem) {
            $this->manager->remove($orderItem);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(['message' => 'Order item not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * Lister tous les items d'une commande
     */
    #[Route('/{orderId}/items', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/orders/{orderId}/items',
        summary: 'Lister les items d\'une commande',
        parameters: [new OA\Parameter(name: 'orderId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Liste des items'),
            new OA\Response(response: 404, description: 'Commande non trouvée')
        ]
    )]
    public function list(int $orderId): JsonResponse
    {
        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        $orderItems = $this->repository->findBy(['order' => $order]);
        
        $responseData = $this->serializer->serialize($orderItems, 'json', [
            AbstractNormalizer::GROUPS => ['orderItem:read']
        ]);

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }
}