<?php

namespace App\Entity;

use App\Repository\RegimeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegimeRepository::class)]
class Regime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name_remige = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameRemige(): ?string
    {
        return $this->name_remige;
    }

    public function setNameRemige(string $name_remige): static
    {
        $this->name_remige = $name_remige;

        return $this;
    }
}
