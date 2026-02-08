<?php

namespace App\Controller;

use App\Entity\Order;
use DatetimeImmutable;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/order', name: 'app_api_order_')]
class OrderController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private OrderRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}
   
    #[Route(methods: 'POST')]
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


    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        $order = $this->repository->findOneBy(['id' => $id]);
        if ($order) {
            $order = $this->serializer->serialize($order, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $order = $this->repository->findOneBy(['id' => $id]);
        if ($order) {
            $order = $this->serializer->deserialize(
                $request->getContent(),
                Order::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $order]
            );
            $order->setUpdatedAt(new DatetimeImmutable());
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $order = $this->repository->findOneBy(['id' => $id]);
        if ($order) {
            $this->order->remove($order);
            $this->order->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
