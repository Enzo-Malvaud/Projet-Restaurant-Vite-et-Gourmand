<?php

namespace App\Entity;

use App\Repository\DishRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DishRepository::class)]
class Dish
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $dish_title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $picture = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $allergens = null;

    #[ORM\Column(length: 255)]
    private ?string $type_of_dish = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDishTitle(): ?string
    {
        return $this->dish_title;
    }

    public function setDishTitle(string $dish_title): static
    {
        $this->dish_title = $dish_title;

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

    public function getAllergens(): ?string
    {
        return $this->allergens;
    }

    public function setAllergens(?string $allergens): static
    {
        $this->allergens = $allergens;

        return $this;
    }

    public function getTypeOfDish(): ?string
    {
        return $this->type_of_dish;
    }

    public function setTypeOfDish(string $type_of_dish): static
    {
        $this->type_of_dish = $type_of_dish;

        return $this;
    }
}
