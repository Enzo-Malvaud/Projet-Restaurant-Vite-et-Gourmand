<?php

namespace App\Entity;

use App\Repository\ThemeMenuRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ThemeMenuRepository::class)]
class ThemeMenu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name_theme = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameTheme(): ?string
    {
        return $this->name_theme;
    }

    public function setNameTheme(string $name_theme): static
    {
        $this->name_theme = $name_theme;

        return $this;
    }
}
