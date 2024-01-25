<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class BrandController extends AbstractController
{
    #[Route('/brands', name: 'app_brands', methods: ['GET'])]
    public function index(BrandRepository $brandRepository): JsonResponse
    {
        $brands = $brandRepository->findAll();

        return $this->json([
            'brands' => $brands,
        ], context: ['groups' => 'brands:read'
        ]);
    }

    #[Route('/brand/{id}', name: 'app_brand_get', methods: ['GET'])]
    public function get(Brand $brand): JsonResponse
    {
        return $this->json($brand,  context: ['groups' => 'brands:read']);
    }

    #[Route('/brands', name: 'app_brand_add', methods: ['POST'])]
    public function add(
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $brand = new Brand();
            $brand->setName($data['name']);

            $em->persist($brand);
            $em->flush();

            return $this->json($brand, context: [
                'groups' => ['brands:read']]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    #[Route('/brand/{id}', name: 'app_brand_update', methods: ['PUT','PATCH'])]
    public function update(
        Brand $brand,
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $brand->setName($data['name']);

            $em->persist($brand);
            $em->flush();

            return $this->json($brand, context: [
                'groups' => ['brands:read']]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/brand/{id}', name: 'app_brand_delete', methods: ['DELETE'])]
    public function delete(Brand $brand, EntityManagerInterface $em): JsonResponse
    {
        try {
            $em->remove($brand);
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
