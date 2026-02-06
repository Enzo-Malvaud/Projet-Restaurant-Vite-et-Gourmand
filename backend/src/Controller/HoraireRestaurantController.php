<?php

namespace App\Controller;

use App\Entity\HoraireRestaurant;
use DatetimeImmutable;
use App\Repository\HoraireRestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/horaire_restaurant', name: 'app_api_horaire_restaurant_')]
class HoraireRestaurantController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private HoraireRestaurantRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}
   
    #[Route(methods: 'POST')]
    public function new(Request $request): JsonResponse
    {
        $horaire_restaurant = $this->serializer->deserialize($request->getContent(), HoraireRestaurant::class, 'json');
        $horaire_restaurant->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($horaire_restaurant);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($horaire_restaurant, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_horaire_restaurant_show',
            ['id' => $horaire_restaurant->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }


    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        $horaire_restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($horaire_restaurant) {
            $responseData = $this->serializer->serialize($horaire_restaurant, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $horaire_restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($horaire_restaurant) {
            $horaire_restaurant = $this->serializer->deserialize(
                $request->getContent(),
                HoraireRestaurant::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $horaire_restaurant]
            );
            $horaire_restaurant->setUpdatedAt(new DatetimeImmutable());
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $horaire_restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($horaire_restaurant) {
            $this->manager->remove($horaire_restaurant);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
