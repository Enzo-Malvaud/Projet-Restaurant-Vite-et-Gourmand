<?php

namespace App\Controller;

use App\Entity\Dish;
use DatetimeImmutable;
use App\Repository\DishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/dish', name: 'app_api_dish_')]
class DishController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private DishRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}
   
    #[Route(methods: 'POST')]
    public function new(Request $request): JsonResponse
    {
        $dish = $this->serializer->deserialize($request->getContent(), Dish::class, 'json');
        $dish->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($dish);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($dish, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_dish_show',
            ['id' => $dish->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        $dish = $this->repository->findOneBy(['id' => $id]);
        if ($dish) {
            $responseData = $this->serializer->serialize($dish, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $dish = $this->repository->findOneBy(['id' => $id]);
        if ($dish) {
            $dish = $this->serializer->deserialize(
                $request->getContent(),
                Dish::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $dish]
            );
            $dish->setUpdatedAt(new DatetimeImmutable());
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $dish = $this->repository->findOneBy(['id' => $id]);
        if ($dish) {
            $this->manager->remove($dish);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
