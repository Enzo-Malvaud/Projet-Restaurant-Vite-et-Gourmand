<?php

namespace App\Controller;

use App\Entity\Notice;
use App\Repository\NoticeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;


#[Route('/api/notices', name: 'app_api_notice_')]
#[OA\Tag(name: 'Notices')]
class NoticeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private NoticeRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/notices',
        summary: 'Lister toutes les notices',
        responses: [
            new OA\Response(response: 200, description: 'Liste des notices')
        ]
    )]
    public function list(): JsonResponse
    {
        $notices = $this->repository->findAll();

        $responseData = $this->serializer->serialize($notices, 'json', [
            AbstractNormalizer::GROUPS => ['notice:read']
        ]);

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }
   
    #[Route('', name: 'new', methods: ['POST'])]
    #[OA\Post(
        path: '/api/notices',
        summary: 'Créer une nouvelle notice',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Excellent service'),
                    new OA\Property(property: 'note', type: 'integer', example: 5),
                    new OA\Property(property: 'description', type: 'string', example: 'Très bon accueil et produits frais')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Notice créée avec succès'),
            new OA\Response(response: 400, description: 'Données invalides')
        ]
    )]
    public function new(Request $request): JsonResponse
    {
   
        $notice = $this->serializer->deserialize($request->getContent(), Notice::class, 'json', [
            AbstractNormalizer::GROUPS => ['notice:write']
        ]);

    
        $this->manager->persist($notice);
        $this->manager->flush();

  
        $responseData = $this->serializer->serialize($notice, 'json', [
            AbstractNormalizer::GROUPS => ['notice:read']
        ]);
        $location = $this->urlGenerator->generate(
            'app_api_notice_show',
            ['id' => $notice->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/notices/{id}',
        summary: 'Récupérer une notice par son ID',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Détails de la notice'),
            new OA\Response(response: 404, description: 'Notice non trouvée')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $notice = $this->repository->findOneBy(['id' => $id]);
        if ($notice) {
 
            $responseData = $this->serializer->serialize($notice, 'json', [
                AbstractNormalizer::GROUPS => ['notice:read']
            ]);
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(['message' => 'Notice not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/notices/{id}',
        summary: 'Modifier une notice existante',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Très excellent'),
                    new OA\Property(property: 'note', type: 'integer', example: 5),
                    new OA\Property(property: 'description', type: 'string', example: 'Expérience fantastique')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Notice mise à jour'),
            new OA\Response(response: 404, description: 'Notice non trouvée')
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $notice = $this->repository->findOneBy(['id' => $id]);
        if ($notice) {

            $this->serializer->deserialize(
                $request->getContent(),
                Notice::class,
                'json',
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $notice,
                    AbstractNormalizer::GROUPS => ['notice:write']
                ]
            );
            $this->manager->flush();

            return new JsonResponse(['message' => 'Notice updated'], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'Notice not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/notices/{id}',
        summary: 'Supprimer une notice',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Notice supprimée'),
            new OA\Response(response: 404, description: 'Notice non trouvée')
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $notice = $this->repository->findOneBy(['id' => $id]);
        if ($notice) {
            $this->manager->remove($notice);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse(['message' => 'Notice not found'], Response::HTTP_NOT_FOUND);
    }
}