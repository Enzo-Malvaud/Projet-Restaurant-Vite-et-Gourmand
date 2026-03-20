<?php

namespace App\Entity;

use App\Repository\MaterialRentalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterialRentalRepository::class)]
#[ORM\HasLifecycleCallbacks] 
class MaterialRental
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column] 
    private ?int $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)] 
    private ?string $unit_price = null;

    #[ORM\ManyToOne(inversedBy: 'materialRentals')] 
    #[ORM\JoinColumn(nullable: false)]
    private ?Material $material = null;

    #[ORM\ManyToOne(inversedBy: 'materialRentals')] 
    #[ORM\JoinColumn(nullable: false)]
    private ?Rental $rental = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
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

    public function getUnitPrice(): ?string
    {
        return $this->unit_price;
    }

    public function setUnitPrice(string $unit_price): static
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
}