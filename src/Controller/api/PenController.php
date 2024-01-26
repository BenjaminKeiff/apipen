<?php

namespace App\Controller\api;

use App\Entity\Pen;
use App\Service\PenService;
use OpenApi\Attributes as OA;
use App\Repository\PenRepository;
use App\Repository\TypeRepository;
use App\Repository\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
#[OA\Tag(name: 'Stylos')]
#[Security(name: 'Bearer')]
class PenController extends AbstractController
{
    #[Route('/pens', name: 'app_pens', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Retourne tous les stylos.',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Pen::class, groups: ['pens:read']))
        )
    )]
    
    public function index(PenRepository $penRepository): JsonResponse
    {

        $pens = $penRepository->findAll();

        return $this->json([
            'pens' => $pens,
        ], context: ['groups' => 'pens:read']
    );
    }

    #[Route('/pen/{id}', name: 'app_pen_get', methods: ['GET'])]
    public function get(Pen $pen): JsonResponse
    {
        return $this->json($pen, context: ['groups' => 'pens:read']);
    }

    #[Route('/pens', name: 'app_pen_add', methods: ['POST'])]
    public function add(
        Request $request,
        PenService $penService
    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $pen = $penService->createFromJsonString($request->getContent());

            return $this->json($pen, context: [
                'groups' => ['pens:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    #[Route('/pen/{id}', name: 'app_pen_update', methods: ['PUT','PATCH'])]
    public function update(
        Pen $pen,
        Request $request,
        PenService $penService
    ): JsonResponse {
        try {
            $penService->updateWithJsonData($pen, $request->getContent());
            
            return $this->json($pen, context: [
                'groups' => ['pens:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/pen/{id}', name: 'app_pen_delete', methods: ['DELETE'])]
    public function delete(Pen $pen, EntityManagerInterface $em): JsonResponse
    {
        try {
            $em->remove($pen);
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
