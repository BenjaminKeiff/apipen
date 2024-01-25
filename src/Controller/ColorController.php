<?php

namespace App\Controller;

use App\Entity\Color;
use App\Repository\ColorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ColorController extends AbstractController
{
    #[Route('/colors', name: 'app_colors', methods: ['GET'])]
    public function index(ColorRepository $colorRepository): JsonResponse
    {
        $colors = $colorRepository->findAll();

        return $this->json([
            'colors' => $colors,
        ], context: ['groups' => 'colors:read']
    );
    }

    #[Route('/color/{id}', name: 'app_color_get', methods: ['GET'])]
    public function get(Color $color): JsonResponse
    {
        return $this->json($color, context: ['groups' => 'colors:read']);
    }

    #[Route('/colors', name: 'app_color_add', methods: ['POST'])]
    public function add(
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $color = new Color();
            $color->setName($data['name']);
            $em->persist($color);
            $em->flush();

            return $this->json($color, context: [
                'groups' => ['colors:read']]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    #[Route('/color/{id}', name: 'app_color_update', methods: ['PUT','PATCH'])]
    public function update(
        Color $color,
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);

            $color->setName($data['name']);

            $em->persist($color);
            $em->flush();

            return $this->json($color, context: [
                'groups' => ['colors:read']]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/color/{id}', name: 'app_color_delete', methods: ['DELETE'])]
    public function delete(Color $color, EntityManagerInterface $em): JsonResponse
    {
        try {
            $em->remove($color);
            $em->flush();
            
            return $this->json([
                'code' => 200,
                'message' => "La couleur à bien été supprimé"
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
