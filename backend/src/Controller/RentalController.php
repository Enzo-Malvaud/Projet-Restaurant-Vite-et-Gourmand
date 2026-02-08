<?php

namespace App\Controller;

use App\Entity\Rental;
use DatetimeImmutable;
use App\Repository\RentalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/rental', name: 'app_api_rental_')]
class RentalController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private RentalRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}
   
    #[Route(methods: 'POST')]
    public function new(Request $request): JsonResponse
    {
        $rental = $this->serializer->deserialize($request->getContent(), Rental::class, 'json');
        $rental->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($rental);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($rental, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_rental_show',
            ['id' => $rental->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }


    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        $rental = $this->repository->findOneBy(['id' => $id]);
        if ($rental) {
            $rental = $this->serializer->serialize($rental, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $rental = $this->repository->findOneBy(['id' => $id]);
        if ($rental) {
            $rental = $this->serializer->deserialize(
                $request->getContent(),
                Rental::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $rental]
            );
            $rental->setUpdatedAt(new DatetimeImmutable());
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $rental = $this->repository->findOneBy(['id' => $id]);
        if ($rental) {
            $this->rental->remove($rental);
            $this->rental->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
