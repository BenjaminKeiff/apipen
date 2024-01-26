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
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;


#[Route('/api')]
#[OA\Tag(name: 'Marques')]
#[Security(name: 'Bearer')]
class BrandController extends AbstractController
{
    #[Route('/brands', name: 'app_brands', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne toutes les marques de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['brands:read']))
        )
    )]
    public function index(BrandRepository $brandRepository): JsonResponse
    {
        $brands = $brandRepository->findAll();

        return $this->json([
            'brands' => $brands,
        ], context: ['groups' => 'brands:read'
        ]);
    }

    #[Route('/brand/{id}', name: 'app_brand_get', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne une marque de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['brands:read']))
        )
    )]
    public function get(Brand $brand): JsonResponse
    {
        return $this->json($brand,  context: ['groups' => 'brands:read']);
    }

    #[Route('/brands', name: 'app_brand_add', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Ajoute une marque de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['brands:read']))
        )
    )]
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
    #[OA\Response(
        response: 200,
        description: 'Modifie une marque de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['brands:read']))
        )
    )]
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
    #[OA\Response(
        response: 200,
        description: 'Supprime une marque de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Brand::class, groups: ['brands:read']))
        )
    )]
    public function delete(Brand $brand, EntityManagerInterface $em): JsonResponse
    {
        try {
            $em->remove($brand);
            $em->flush();
            
            return $this->json([
                'code' => 200,
                'message' => "La marque à bien été supprimé. (Seulement s'il n'a aucune affiliation avec un stylo)"
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
