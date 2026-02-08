<?php

namespace App\Controller;

use App\Entity\Material;
use DatetimeImmutable;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/material', name: 'app_api_material_')]
class MaterialController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private MaterialRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}
   
    #[Route(methods: 'POST')]
    public function new(Request $request): JsonResponse
    {
        $material = $this->serializer->deserialize($request->getContent(), Material::class, 'json');
        $material->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($material);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($material, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_material_show',
            ['id' => $material->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }


    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        $material = $this->repository->findOneBy(['id' => $id]);
        if ($material) {
            $responseData = $this->serializer->serialize($material, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $material = $this->repository->findOneBy(['id' => $id]);
        if ($material) {
            $material = $this->serializer->deserialize(
                $request->getContent(),
                Material::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $material]
            );
            $material->setUpdatedAt(new DatetimeImmutable());
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $material = $this->repository->findOneBy(['id' => $id]);
        if ($material) {
            $this->manager->remove($material);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
