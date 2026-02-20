<?php

namespace App\Entity;

use App\Repository\MaterialRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterialRepository::class)]
class Material
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $daily_rental_price = null;

    #[ORM\Column]
    private ?int $total_quantity = null;

    #[ORM\Column]
    private ?int $quantity_available = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $picture = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $caution = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $rental_condition = null;
 
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDailyRentalPrice(): ?string
    {
        return $this->daily_rental_price;
    }

    public function setDailyRentalPrice(string $daily_rental_price): static
    {
        $this->daily_rental_price = $daily_rental_price;

        return $this;
    }

    public function getTotalQuantity(): ?int
    {
        return $this->total_quantity;
    }

    public function setTotalQuantity(int $total_quantity): static
    {
        $this->total_quantity = $total_quantity;

        return $this;
    }

    public function getQuantityAvailable(): ?int
    {
        return $this->quantity_available;
    }

    public function setQuantityAvailable(int $quantity_available): static
    {
        $this->quantity_available = $quantity_available;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getCaution(): ?string
    {
        return $this->caution;
    }

    public function setCaution(string $caution): static
    {
        $this->caution = $caution;

        return $this;
    }

    public function getRentalCondition(): ?string
    {
        return $this->rental_condition;
    }

    public function setRentalCondition(?string $rental_condition): static
    {
        $this->rental_condition = $rental_condition;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
