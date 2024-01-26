<?php

namespace App\Controller;

use App\Entity\Material;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;


#[Route('/api')]
#[OA\Tag(name: 'Materiaux')]
#[Security(name: 'Bearer')]
class MaterialController extends AbstractController
{
    #[Route('/materials', name: 'app_materials', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne tous les materiaux de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['materials:read']))
        )
    )]
    public function index(MaterialRepository $materialRepository): JsonResponse
    {
        $materials = $materialRepository->findAll();

        return $this->json([
            'materials' => $materials,
        ], context: ['groups' => 'materials:read']
    );
    }

    #[Route('/material/{id}', name: 'app_material_get', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne materiau de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['materials:read']))
        )
    )]
    public function get(Material $material): JsonResponse
    {
        return $this->json($material, context: ['groups' => 'materials:read']);
    }

    #[Route('/materials', name: 'app_material_add', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Ajoute un materiau de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['materials:read']))
        )
    )]
    public function add(
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $material = new Material();
            $material->setName($data['name']);
            $em->persist($material);
            $em->flush();

            return $this->json($material, context: [
                'groups' => ['materials:read']]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    #[Route('/material/{id}', name: 'app_material_update', methods: ['PUT','PATCH'])]
    #[OA\Response(
        response: 200,
        description: 'Modifie un materiau de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['materials:read']))
        )
    )]
    public function update(
        Material $material,
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $material->setName($data['name']);

            $em->persist($material);
            $em->flush();

            return $this->json($material, context: [
                'groups' => ['materials:read']]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/material/{id}', name: 'app_material_delete', methods: ['DELETE'])]
    #[OA\Response(
        response: 200,
        description: "Supprime un materiau de stylo. (Seulement s'il n'a aucune affiliation avec un stylo)",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Material::class, groups: ['materials:read']))
        )
    )]
    public function delete(Material $material, EntityManagerInterface $em): JsonResponse
    {
        try {
            $em->remove($material);
            $em->flush();
            
            return $this->json([
                'code' => 200,
                'message' => "Le stylo à bien été supprimé"
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
