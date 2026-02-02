<?php

namespace App\Entity;

use App\Repository\RentalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentalRepository::class)]
class Rental
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_rental = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_of_rendering = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $rendering_time = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $rental_price = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $date_of_modification = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Adresse $adresse = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Material $material = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateRental(): ?\DateTimeImmutable
    {
        return $this->date_rental;
    }

    public function setDateRental(\DateTimeImmutable $date_rental): static
    {
        $this->date_rental = $date_rental;

        return $this;
    }

    public function getDateOfRendering(): ?\DateTimeImmutable
    {
        return $this->date_of_rendering;
    }

    public function setDateOfRendering(\DateTimeImmutable $date_of_rendering): static
    {
        $this->date_of_rendering = $date_of_rendering;

        return $this;
    }

    public function getRenderingTime(): ?\DateTimeImmutable
    {
        return $this->rendering_time;
    }

    public function setRenderingTime(\DateTimeImmutable $rendering_time): static
    {
        $this->rendering_time = $rendering_time;

        return $this;
    }

    public function getRentalPrice(): ?string
    {
        return $this->rental_price;
    }

    public function setRentalPrice(string $rental_price): static
    {
        $this->rental_price = $rental_price;

        return $this;
    }

    public function getDateOfModification(): ?\DateTimeImmutable
    {
        return $this->date_of_modification;
    }

    public function setDateOfModification(?\DateTimeImmutable $date_of_modification): static
    {
        $this->date_of_modification = $date_of_modification;

        return $this;
    }

    public function getAdresse(): ?Adresse
    {
        return $this->adresse;
    }

    public function setAdresse(?Adresse $adresse): static
    {
        $this->adresse = $adresse;

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
