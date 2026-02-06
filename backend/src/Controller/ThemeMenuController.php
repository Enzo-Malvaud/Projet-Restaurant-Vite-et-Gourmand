<?php

namespace App\Controller;

use App\Entity\ThemeMenu;
use DatetimeImmutable;
use App\Repository\ThemeMenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/theme_menu', name: 'app_api_theme_menu_')]
class ThemeMenuController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ThemeMenuRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}
   
    #[Route(methods: 'POST')]
    public function new(Request $request): JsonResponse
    {
        $theme_menu = $this->serializer->deserialize($request->getContent(), ThemeMenu::class, 'json');
        $theme_menu->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($theme_menu);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($theme_menu, 'json');
        $location = $this->urlGenerator->generate(
            'app_api_theme_menu_show',
            ['id' => $theme_menu->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }


    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(int $id): JsonResponse
    {
        $theme_menu = $this->repository->findOneBy(['id' => $id]);
        if ($theme_menu) {
            $theme_menu = $this->serializer->serialize($theme_menu, 'json');

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name: 'edit', methods: 'PUT')]
    public function edit(int $id, Request $request): JsonResponse
    {
        $theme_menu = $this->repository->findOneBy(['id' => $id]);
        if ($theme_menu) {
            $theme_menu = $this->serializer->deserialize(
                $request->getContent(),
                HoraireRestaurant::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $theme_menu]
            );
            $theme_menu->setUpdatedAt(new DatetimeImmutable());
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }


    #[Route('/{id}', name: 'delete', methods: 'DELETE')]
    public function delete(int $id): JsonResponse
    {
        $theme_menu = $this->repository->findOneBy(['id' => $id]);
        if ($theme_menu) {
            $this->theme_menu->remove($theme_menu);
            $this->theme_menu->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
