<?php

namespace App\Service;

use Faker\Factory;
use App\Entity\Pen;
use App\Repository\BrandRepository;
use App\Repository\ColorRepository;
use App\Repository\MaterialRepository;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;

class PenService 
{

    public function __construct(
        private EntityManagerInterface $em,
        private MaterialRepository $materialRepository,
        private TypeRepository $typeRepository,
        private ColorRepository $colorRepository,
        private BrandRepository $brandRepository
    ){}

    public function createFromArray(array $data): Pen
    {
        $faker = Factory::create();

        // On traite les données pour créer un nouveau Stylo
        $pen = new Pen();
        $pen->setName($data['name']);
        $pen->setPrice($data['price']);
        $pen->setDescription($data['description']);
        $pen->setReference($faker->unique()->ean13);

        // Récupération du type de stylo
        if(!empty($data['type']))
        {
            $type = $this->typeRepository->find($data['type']);

            if(!$type)
                throw new \Exception("Le type renseignée n'existe pas");

            $pen->setType($type);
        }

        // Récupération de la marque
        if (!empty($data['brand'])) {
            $brand = $this->brandRepository->find($data['brand']);
    
            if (!$brand) {
                throw new \Exception("La marque renseignée n'existe pas");
            }
    
            $pen->setBrand($brand);
        }

        // Récupération de la couleur
        if(!empty($data['color']))
        {
            $color = $this->colorRepository->find($data['color']);

            if(!$color)
                throw new \Exception("La couleur renseigné n'existe pas");

            $pen->addColor($color);
        }

        // Récupération du matériel
        if(!empty($data['material']))
        {
            $material = $this->materialRepository->find($data['material']);

            if(!$material)
                throw new \Exception("Le matériel renseigné n'existe pas");

            $pen->setMaterial($material);
        }

        $this->em->persist($pen);
        $this->em->flush();

        return $pen;
    }

    public function createFromJsonString(string $jsonString)
    {
        $data = json_decode($jsonString, true);
        return $this->createFromArray($data);
    }

    public function update(Pen $pen, array $data): void
    {
        // On traite les données pour créer un nouveau Stylo

        if(!empty($data['name']))
            $pen->setName($data['name']);

        if(!empty($data['price']))
            $pen->setPrice($data['price']);

        if(!empty($data['description']))
            $pen->setDescription($data['description']);

        // Récupération du type de stylo
        if(!empty($data['type']))
        {
            $type = $this->typeRepository->find($data['type']);

            // On vérifie le type pour que ça ne soit pas vide
            if(!$type)
                throw new \Exception("Le type renseigné n'existe pas");

            $pen->setType($type);
        }

        // Récupération de la marque
        if (!empty($data['brand'])) {
            $brand = $this->brandRepository->find($data['brand']);
    
            if (!$brand) {
                throw new \Exception("La marque renseignée n'existe pas");
            }
    
            $pen->setBrand($brand);
        }

        // Récupération de la couleur
        if(!empty($data['color']))
        {
            $color = $this->colorRepository->find($data['color']);

            if(!$color)
                throw new \Exception("La couleur renseigné n'existe pas");

            $pen->addColor($color);
        }

        // Récupération du matériel
        if(!empty($data['material']))
        {
            $material = $this->materialRepository->find($data['material']);

            if(!$material)
                throw new \Exception("Le matériel renseigné n'existe pas");

            $pen->setMaterial($material);
        }

        $this->em->persist($pen);
        $this->em->flush();

    }

    /**
     * @param Pen $pen
     * @param string $jsonData
     * @return void
     */
    public function updateWithJsonData(Pen $pen, string $jsonData): void
    {
        $data = json_decode($jsonData, true);
        $this->update($pen, $data);
    }

}