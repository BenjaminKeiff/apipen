<?php

namespace App\Controller;

use App\Entity\Pen;
use App\Repository\MaterialRepository;
use App\Repository\PenRepository;
use App\Repository\TypeRepository;
use Doctrine\DBAL\Driver\IBMDB2\Exception\Factory;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class PenController extends AbstractController
{
    #[Route('/pens', name: 'app_pens', methods: ['GET'])]
    public function index(PenRepository $penRepository): JsonResponse
    {

        $pens = $penRepository->findAll();

        return $this->json([
            'pens' => $pens,
        ], context: ['groups' => 'pens:read']
    );
    }

    #[Route('/pen/{id}', name: 'app_pen_get')]
    public function get(Pen $pen): JsonResponse
    {
        return $this->json($pen, context: ['groups' => 'pens:read']);
    }

    #[Route('/pens', name: 'app_pen_add', methods: ['POST'])]
    public function add(
        Request $request,
        EntityManagerInterface $em,
        TypeRepository $typeRepository,
        MaterialRepository $materialRepository,
    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            $faker = \Faker\Factory::create();

            // On traite les données pour créer un nouveau Stylo
            $pen = new Pen();
            $pen->setName($data['name']);
            $pen->setPrice($data['price']);
            $pen->setDescription($data['description']);
            $pen->setReference($faker->unique()->ean13);

            // Récupération du type de stylo
            if(!empty($data['type']))
            {
                $type = $typeRepository->find($data['type']);

                if(!$type)
                    throw new \Exception("Le type renseigné n'existe pas");

                $pen->setType($type);
            }

            // Récupération du matériel
            if(!empty($data['material']))
            {
                $material = $materialRepository->find($data['material']);

                if(!$material)
                    throw new \Exception("Le matériel renseigné n'existe pas");

                $pen->setMaterial($material);
            }

            $em->persist($pen);
            $em->flush();

            return $this->json($pen, context: [
                'groups' => ['pen:read'],
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    #[Route('/pen/{id}', name: 'app_pen_add', methods: ['PUT','PATCH'])]
    public function update(
        Pen $pen,
        Request $request,
        EntityManagerInterface $em,
        TypeRepository $typeRepository,
        MaterialRepository $materialRepository,
    ): JsonResponse {
        try {
            // On recupère les données du corps de la requête
            // Que l'on transforme ensuite en tableau associatif
            $data = json_decode($request->getContent(), true);

            // On traite les données pour créer un nouveau Stylo
            $pen->setName($data['name']);
            $pen->setPrice($data['price']);
            $pen->setDescription($data['description']);

            // Récupération du type de stylo
            if(!empty($data['type']))
            {
                $type = $typeRepository->find($data['type']);

                if(!$type)
                    throw new \Exception("Le type renseigné n'existe pas");

                $pen->setType($type);
            }

            // Récupération du matériel
            if(!empty($data['material']))
            {
                $material = $materialRepository->find($data['material']);

                if(!$material)
                    throw new \Exception("Le matériel renseigné n'existe pas");

                $pen->setMaterial($material);
            }

            $em->persist($pen);
            $em->flush();

            return $this->json($pen, context: [
                'groups' => ['pen:read'],
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
