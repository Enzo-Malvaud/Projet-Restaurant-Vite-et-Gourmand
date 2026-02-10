<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\{Items, MediaType, RequestBody, Schema, Property, Response as attributeResponse};


#[Route('/api', name: 'app_api_')]
class SecurityController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private SerializerInterface $serializer, private UserRepository $repository) {}

    #[Route('/registration', name: 'registration', methods: 'POST')]

    #[OA\Post(
        path: '/api/registration',
        summary: 'Inscription d\'un nouveau utilisateur',
        requestBody: new RequestBody(
            required: true,
            description: 'Données de l\'utilisateur à incrire',
            content: [new MediaType(
                mediaType: "application/json",
                schema: new Schema(
                    type: 'object',
                    properties: [
                        new Property(
                            property: "username",
                            type: "string",
                            example: "adresse@email.com"
                        ),
                        new Property(
                            property: "firstName",
                            type: "string",
                            example: "User"
                        ),
                        new Property(
                            property: "lastName",
                            type: "string",
                            example: "User_lastName"
                        ),
                        new Property(
                            property: "password",
                            type: "string",
                            example: "123-password"
                        )

                    ]
                )
            )]
        ),
        responses: [new attributeResponse(
            response: 201,
            description: 'Utilisateur inscrit avec succès',
            content: [new MediaType(
                mediaType: "application/json",
                schema: new Schema(
                    type: "object",
                    properties: [
                        new Property(
                            property: 'username',
                            type: 'string',
                            example: 'Nom d\'utilisateur'
                        ),
                        new Property(
                            property: 'api_token',
                            type: 'string',
                            example: 'qg5g56rtg6gtgt6tgz46t4z'
                        ),
                        new Property(
                            property: "roles",
                            type: "array",
                            items: new Items(
                                type: 'string',
                                example: 'ROLE_USER'
                            )
                        )
                    ]

                )

            )]
        )]
    )]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
        $user->setCreatedAt(new DateTimeImmutable());

        $this->manager->persist($user);
        $this->manager->flush();
        return new JsonResponse(
            ['user'  => $user->getUserIdentifier(), 'apiToken' => $user->getApiToken(), 'roles' => $user->getRoles()],
            Response::HTTP_CREATED
        );
    }

       #[Route('/login', name: "login", methods: 'POST')]
    #[OA\Post(
        path: '/api/login',
        summary: 'Connecter un utilisateur',
        requestBody: new RequestBody(
            required: true,
            description: 'Données de connexion',
            content: [
                new MediaType(
                    mediaType: "application/json",
                    schema: new Schema(
                        type: 'object',
                        properties: [
                            new Property(
                                property: "username",
                                type: "string",
                                example: "adresse@email.com"
                            ),
                            new Property(
                                property: "firstName",
                                type: "string",
                                example: "User"
                            ),
                            new Property(
                                property: "lastName",
                                type: "string",
                                example: "User_lastName"
                            ),
                            new Property(
                                property: "password",
                                type: "string",
                                example: "123-password"
                            )

                        ]
                    )
                )
            ]
        ),
        
        responses: [
            new attributeResponse(
                response: 200,
                description: 'Utilisateur connecté avec succès',
                content: [
                    new MediaType(
                        mediaType: "application/json",
                        schema: new Schema(
                            type: "object",
                            properties: [
                                new Property(
                                    property: 'username',
                                    type: 'string',
                                    example: 'Nom d\'utilisateur'
                                ),
                                new Property(
                                    property: 'api_token',
                                    type: 'string',
                                    example: 'qg5g56rtg6gtgt6tgz46t4z'
                                ),
                                new Property(
                                    property: "roles",
                                    type: "array",
                                    items: new Items(
                                        type: 'string',
                                        example: 'ROLE_USER'
                                    )
                                )
                            ]
                        )
                    )
                ]
            )
        ]
    )]
//#[CurrentUser] permet de faire comme une fonction d'un reposiory, findOneBy, chercher en base l'utilisateur qui veut se connecter
    public function login(#[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return new JsonResponse(['message' => 'missing credentials'], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'user'  => $user->getUserIdentifier(),
            'apiToken' => $user->getApiToken(),
            'roles' => $user->getRoles(),
        ]);
    }




    #[Route('/me', name: 'me_show', methods: 'GET')]
    public function show(#[CurrentUser] ?User $user): JsonResponse
    {
        if ($user) {
            $json = $this->serializer->serialize($user, 'json');

            return new JsonResponse($json, Response::HTTP_OK, [], true);
        }

        return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    }

    
    #[Route('/edit', name: 'edit_updated', methods: 'PUT')]
    public function updated(#[CurrentUser] ?User $user, Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {

        if ($user) {
            $this->serializer->deserialize(
                $request->getContent(),
                User::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $user]
            );
            $data = json_decode($request->getContent(), true);

            if (!empty($data['password'])) {
                $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
            }
            $user->setUpdatedAt(new DateTimeImmutable());
            $this->manager->flush();

            return new JsonResponse(['message' => 'User updated'], Response::HTTP_OK);
        }

        return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    }
}