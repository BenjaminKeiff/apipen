<?php

namespace App\Controller;

use App\Entity\Material;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class MaterialController extends AbstractController
{
    #[Route('/materials', name: 'app_materials', methods: ['GET'])]
    public function index(MaterialRepository $materialRepository): JsonResponse
    {
        $materials = $materialRepository->findAll();

        return $this->json([
            'materials' => $materials,
        ], context: ['groups' => 'materials:read']
    );
    }

    #[Route('/material/{id}', name: 'app_material_get', methods: ['GET'])]
    public function get(Material $material): JsonResponse
    {
        return $this->json($material, context: ['groups' => 'materials:read']);
    }

    #[Route('/materials', name: 'app_material_add', methods: ['POST'])]
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
    public function delete(Material $material, EntityManagerInterface $em): JsonResponse
    {
        try {
            $em->remove($material);
            $em->flush();
            
            return $this->json([
                'code' => 200,
                'message' => "Le stylot à bien été supprimé"
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
