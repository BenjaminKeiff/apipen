<?php

namespace App\Controller;

use App\Entity\Type;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;


#[Route('/api')]
#[OA\Tag(name: 'Types')]
#[Security(name: 'Bearer')]
class TypeController extends AbstractController
{
    #[Route('/types', name: 'app_types', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne tous les types de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Type::class, groups: ['types:read']))
        )
    )]
    public function index(TypeRepository $typeRepository): JsonResponse
    {
        $types = $typeRepository->findAll();

        return $this->json([
            'types' => $types,
        ], context: ['groups' => 'types:read']
    );
    }

    #[Route('/type/{id}', name: 'app_type_get', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne un type de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Type::class, groups: ['types:read']))
        )
    )]
    public function get(Type $type): JsonResponse
    {
        return $this->json($type, context: ['groups' => 'types:read']);
    }

    #[Route('/types', name: 'app_type_add', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Ajoute un type de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Type::class, groups: ['types:read']))
        )
    )]
    public function add(
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $type = new Type();
            $type->setName($data['name']);

            $em->persist($type);
            $em->flush();

            return $this->json($type, context: [
                'groups' => ['types:read']]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    #[Route('/type/{id}', name: 'app_type_update', methods: ['PUT','PATCH'])]
    #[OA\Response(
        response: 200,
        description: 'Modifie un type de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Type::class, groups: ['types:read']))
        )
    )]
    public function update(
        Type $type,
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $type->setName($data['name']);

            $em->persist($type);
            $em->flush();

            return $this->json($type, context: [
                'groups' => ['types:read']]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/type/{id}', name: 'app_type_delete', methods: ['DELETE'])]
    #[OA\Response(
        response: 200,
        description: "Supprime un type de stylo. (Seulement s'il n'a aucune affiliation avec un stylo)",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Type::class, groups: ['types:read']))
        )
    )]
    public function delete(Type $type, EntityManagerInterface $em): JsonResponse
    {
        try {
            $em->remove($type);
            $em->flush();
            
            return $this->json([
                'code' => 200,
                'message' => "Le type à bien été supprimé"
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
