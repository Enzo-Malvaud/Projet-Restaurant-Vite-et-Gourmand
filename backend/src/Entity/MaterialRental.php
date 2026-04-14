<?php

namespace App\Entity;

use App\Repository\MaterialRentalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MaterialRentalRepository::class)]
#[ORM\HasLifecycleCallbacks] 
class MaterialRental
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['materialRental:read'])]
    private ?int $id = null;


    #[ORM\Column]
    #[Groups(['materialRental:read', 'materialRental:write'])]
    private ?int $quantity = null;


    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['materialRental:read'])]
    private ?float $unit_price = null;


    #[ORM\ManyToOne(inversedBy: 'materialRentals')] 
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['materialRental:write'])]
    private ?Material $material = null;


    #[ORM\ManyToOne(inversedBy: 'materialRentals')] 
    #[ORM\JoinColumn(nullable: false)]
    private ?Rental $rental = null;


    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['materialRental:read'])]
    private ?\DateTimeImmutable $createdAt = null;


    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }


    public function getUnitPrice(): ?float
    {
        return $this->unit_price;
    }


    public function setUnitPrice(float $unit_price): static
    {
        $this->unit_price = $unit_price;
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

    public function getRental(): ?Rental
    {
        return $this->rental;
    }

    public function setRental(?Rental $rental): static
    {
        $this->rental = $rental;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }


    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}