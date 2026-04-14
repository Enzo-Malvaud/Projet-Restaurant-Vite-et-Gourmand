<?php

namespace App\Controller;

use App\Entity\OrderItem;
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

    #[Route('/{orderId}/items', name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/orders/{orderId}/items',
        summary: 'Ajouter un item à une commande',
        parameters: [new OA\Parameter(name: 'orderId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'menu', type: 'integer', example: 1),
                    new OA\Property(property: 'quantity', type: 'integer', example: 2),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Item créé'),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 404, description: 'Commande ou Menu introuvable'),
            new OA\Response(response: 409, description: 'Stock insuffisant'),
        ]
    )]
    public function new(int $orderId, Request $request): JsonResponse
    {
        // 1. Vérification de la commande
        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['message' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }

        // 2. Validation du menu
        if (!isset($data['menu']) || !is_int($data['menu'])) {
            return new JsonResponse(['message' => 'Field "menu" is required and must be an integer'], Response::HTTP_BAD_REQUEST);
        }
        $menu = $this->menuRepository->find($data['menu']);
        if (!$menu) {
            return new JsonResponse(['message' => sprintf('Menu #%d not found', $data['menu'])], Response::HTTP_NOT_FOUND);
        }

        // 3. Validation de la quantité
        $quantity = $data['quantity'] ?? null;
        if (!is_int($quantity) || $quantity < 1) {
            return new JsonResponse(['message' => 'Field "quantity" must be a positive integer'], Response::HTTP_BAD_REQUEST);
        }

        // 4. Vérification du stock
        if ($menu->getRemainingQuantity() < $quantity) {
            return new JsonResponse([
                'message' => sprintf(
                    'Only %d unit(s) available for menu "%s"',
                    $menu->getRemainingQuantity(),
                    $menu->getTitleMenu()
                )
            ], Response::HTTP_CONFLICT);
        }

        // 5. Création de l'OrderItem
        $orderItem = new OrderItem();
        $orderItem->setMenu($menu);
        $orderItem->setQuantity($quantity);
        $orderItem->setPriceUnit((float) $menu->getPriceMenu());
        $orderItem->setOrder($order);

        // 6. Décrémentation du stock
        $menu->setRemainingQuantity($menu->getRemainingQuantity() - $quantity);

        // 7. Recalcul des prix de la commande parente
        $newOrderPrice = (float) $order->getOrderPrice() + ((float) $menu->getPriceMenu() * $quantity);
        $order->setOrderPrice($newOrderPrice);
        $order->setTotalPrice($newOrderPrice + (float) $order->getDeliveryPrice());

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

    #[Route('/{orderId}/items/{itemId}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/orders/{orderId}/items/{itemId}',
        summary: 'Modifier la quantité d\'un item de commande',
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
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 404, description: 'Item non trouvé'),
            new OA\Response(response: 409, description: 'Stock insuffisant'),
        ]
    )]
    public function edit(int $orderId, int $itemId, Request $request): JsonResponse
    {
        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            return new JsonResponse(['message' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        $orderItem = $this->repository->findOneBy(['id' => $itemId, 'order' => $order]);
        if (!$orderItem) {
            return new JsonResponse(['message' => 'Order item not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $newQuantity = $data['quantity'] ?? null;

        if (!is_int($newQuantity) || $newQuantity < 1) {
            return new JsonResponse(['message' => 'Field "quantity" must be a positive integer'], Response::HTTP_BAD_REQUEST);
        }

        $menu = $orderItem->getMenu();
        $oldQuantity = $orderItem->getQuantity();
        $diff = $newQuantity - $oldQuantity; // positif = on commande plus, négatif = on réduit

        // Vérification stock uniquement si on augmente la quantité
        if ($diff > 0 && $menu->getRemainingQuantity() < $diff) {
            return new JsonResponse([
                'message' => sprintf(
                    'Only %d additional unit(s) available for menu "%s"',
                    $menu->getRemainingQuantity(),
                    $menu->getTitleMenu()
                )
            ], Response::HTTP_CONFLICT);
        }

        // Mise à jour du stock
        $menu->setRemainingQuantity($menu->getRemainingQuantity() - $diff);

        // Mise à jour de l'item
        $orderItem->setQuantity($newQuantity);

        // Recalcul des prix de la commande parente
        $newOrderPrice = (float) $order->getOrderPrice() + ($orderItem->getPriceUnit() * $diff);
        $order->setOrderPrice($newOrderPrice);
        $order->setTotalPrice($newOrderPrice + (float) $order->getDeliveryPrice());

        $this->manager->flush();

        return new JsonResponse(['message' => 'Order item updated'], Response::HTTP_OK);
    }

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
        if (!$orderItem) {
            return new JsonResponse(['message' => 'Order item not found'], Response::HTTP_NOT_FOUND);
        }

        $menu = $orderItem->getMenu();
        $quantity = $orderItem->getQuantity();

        // Réincrémentation du stock
        $menu->setRemainingQuantity($menu->getRemainingQuantity() + $quantity);

        // Recalcul des prix de la commande parente
        $newOrderPrice = (float) $order->getOrderPrice() - ($orderItem->getPriceUnit() * $quantity);
        $order->setOrderPrice($newOrderPrice);
        $order->setTotalPrice($newOrderPrice + (float) $order->getDeliveryPrice());

        $this->manager->remove($orderItem);
        $this->manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

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