<?php

namespace App\Entity;

use App\Repository\HoraireRestaurantRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HoraireRestaurantRepository::class)]
class HoraireRestaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $day = null;

    #[ORM\Column(length: 255)]
    private ? \DateTimeImmutable $opening_hour = null;

    #[ORM\Column(length: 255)]
    private ? \DateTimeImmutable $closing_hour = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): static
    {
        $this->day = $day;

        return $this;
    }

    public function getOpeningHour(): ?\DateTimeImmutable
    {
        return $this->opening_hour;
    }

    public function setOpeningHour(\DateTimeImmutable $opening_hour): static
    {
        $this->opening_hour = $opening_hour;
        return $this;
    }

    public function getClosingHour(): ?\DateTimeImmutable
    {
        return $this->closing_hour;
    }

    public function setClosingHour(\DateTimeImmutable $closing_hour): static
    {
        $this->closing_hour = $closing_hour;
        return $this;
    }
}
