<?php

namespace App\Controller;

use App\Entity\ThemeMenu;
use DateTimeImmutable; // Corrigé
use App\Repository\ThemeMenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

#[Route('/api/theme_menu', name: 'app_api_theme_menu_')]
#[OA\Tag(name: 'Themes Menus')]
class ThemeMenuController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private ThemeMenuRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}
   
    #[Route('', name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/theme_menu',
        summary: 'Créer un nouveau thème de menu',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Thème Été'),
                    new OA\Property(property: 'description', type: 'string', example: 'Menu saisonnier')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Thème créé')
        ]
    )]
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

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/theme_menu/{id}',
        summary: 'Afficher un thème par son ID',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Succès'),
            new OA\Response(response: 404, description: 'Non trouvé')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $theme_menu = $this->repository->findOneBy(['id' => $id]);
        if ($theme_menu) {
            $responseData = $this->serializer->serialize($theme_menu, 'json'); // Corrigé : renommage pour éviter conflit

            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/theme_menu/{id}',
        summary: 'Modifier un thème existant',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Nouveau Nom')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Modifié'),
            new OA\Response(response: 404, description: 'Non trouvé')
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $theme_menu = $this->repository->findOneBy(['id' => $id]);
        if ($theme_menu) {
            $this->serializer->deserialize(
                $request->getContent(),
                ThemeMenu::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $theme_menu]
            );
            $theme_menu->setUpdatedAt(new DateTimeImmutable());
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_OK);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/theme_menu/{id}',
        summary: 'Supprimer un thème',
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 204, description: 'Supprimé'),
            new OA\Response(response: 404, description: 'Non trouvé')
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $theme_menu = $this->repository->findOneBy(['id' => $id]);
        if ($theme_menu) {
            $this->manager->remove($theme_menu); // Corrigé : utilisation du manager
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}