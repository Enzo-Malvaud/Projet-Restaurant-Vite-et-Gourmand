<?php

namespace App\Controller;

use App\Entity\Adresse;
use DatetimeImmutable;
use App\Repository\AdresseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/adresse', name: 'app_api_adresse_')]
class AdresseController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private AdresseRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}
   
    #[Route(methods: 'POST')]
    public function new(Request $request): JsonResponse
    {
        $adresse = $this->serializer->deserialize($request->getContent(), Adresse::class, 'json');
        $adresse->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($adresse);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($adresse, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_adresse_show',
            ['id' => $adresse->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        $adresse = $this->repository->findOneBy(['id' => $id]);
        if ($adresse) {
            $responseData = $this->serializer->serialize($adresse, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $adresse = $this->repository->findOneBy(['id' => $id]);
        if ($adresse) {
            $adresse = $this->serializer->deserialize(
                $request->getContent(),
                Adresse::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $adresse]
            );
            $adresse->setUpdatedAt(new DatetimeImmutable());
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $adresse = $this->repository->findOneBy(['id' => $id]);
        if ($adresse) {
            $this->manager->remove($adresse);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
