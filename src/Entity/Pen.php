<?php

namespace App\Entity;

use App\Repository\PenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PenRepository::class)]
class Pen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('pens:read')]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups('pens:read')]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups('pens:read')]
    private ?string $price = null;

    #[ORM\Column(length: 255)]
    #[Groups('pens:read')]
    private ?string $description = null;

    #[ORM\Column(length: 10, unique: true)]
    #[Groups('pens:read')]
    private ?string $reference = null;

    #[ORM\ManyToOne(inversedBy: 'pens')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('pens:read')]
    private ?Type $type = null;

    #[ORM\ManyToMany(targetEntity: Color::class, inversedBy: 'pens')]
    #[Groups('pens:read')]
    private Collection $colors;

    #[ORM\ManyToOne(inversedBy: 'pens')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('pens:read')]
    private ?Brand $brand = null;

    #[ORM\ManyToOne(inversedBy: 'pens')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('pens:read')]
    private ?Material $material = null;

    public function __construct()
    {
        $this->colors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, color>
     */
    public function getColors(): Collection
    {
        return $this->colors;
    }

    public function addColor(Color $color): static
    {
        if (!$this->colors->contains($color)) {
            $this->colors->add($color);
        }

        return $this;
    }

    public function removeColor(Color $color): static
    {
        $this->colors->removeElement($color);

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): static
    {
        $this->material = $material;

        return $this;
    }
}
