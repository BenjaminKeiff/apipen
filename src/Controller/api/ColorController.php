<?php

namespace App\Controller\api;

use App\Entity\Color;
use App\Repository\ColorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;


#[Route('/api')]
#[OA\Tag(name: 'Couleurs')]
#[Security(name: 'Bearer')]
class ColorController extends AbstractController
{
    #[Route('/colors', name: 'app_colors', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne toutes les couleurs de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Color::class, groups: ['colors:read']))
        )
    )]
    public function index(ColorRepository $colorRepository): JsonResponse
    {
        $colors = $colorRepository->findAll();

        return $this->json([
            'colors' => $colors,
        ], context: ['groups' => 'colors:read']
    );
    }

    #[Route('/color/{id}', name: 'app_color_get', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne une couleur de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Color::class, groups: ['colors:read']))
        )
    )]
    public function get(Color $color): JsonResponse
    {
        return $this->json($color, context: ['groups' => 'colors:read']);
    }

    #[Route('/colors', name: 'app_color_add', methods: ['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Ajoute une couleur de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Color::class, groups: ['colors:read']))
        )
    )]
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
    #[OA\Response(
        response: 200,
        description: 'Modifie une couleur de stylo.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Color::class, groups: ['colors:read']))
        )
    )]
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
    #[OA\Response(
        response: 200,
        description: "Supprime une couleur de stylo. (Seulement s'il n'a aucune affiliation avec un stylo)",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Color::class, groups: ['colors:read']))
        )
    )]
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
