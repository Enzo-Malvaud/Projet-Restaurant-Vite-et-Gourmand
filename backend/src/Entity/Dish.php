<?php

namespace App\Entity;

use App\Repository\DishRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: DishRepository::class)]
#[ORM\HasLifecycleCallbacks] 
class Dish
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['dish:read'])]
    private ?int $id = null;


    #[ORM\Column(length: 255)]
    #[Groups(['dish:read', 'dish:write'])]
    private ?string $dish_title = null;


    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['dish:read', 'dish:write'])]
    private ?string $description = null;


    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['dish:read', 'dish:write'])]
    private ?float $price = null;


    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['dish:read', 'dish:write'])]
    private ?string $picture = null;

 
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['dish:read', 'dish:write'])]
    private ?string $allergens = null;


    #[ORM\Column(length: 255)]
    #[Groups(['dish:read', 'dish:write'])]
    private ?string $type_of_dish = null;


    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['dish:read'])]
    private ?\DateTimeImmutable $createdAt = null;


    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['dish:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Menu>
     */
    #[ORM\ManyToMany(targetEntity: Menu::class, mappedBy: 'dishs')]
    private Collection $menus;

    public function __construct()
    {
        $this->menus = new ArrayCollection();
    }


    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }


    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

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


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }


    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;
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

    /**
     * @return Collection<int, Menu>
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function addMenu(Menu $menu): static
    {
        if (!$this->menus->contains($menu)) {
            $this->menus->add($menu);
            $menu->addDish($this); 
        }
        return $this;
    }

    public function removeMenu(Menu $menu): static
    {
        if ($this->menus->removeElement($menu)) {
            $menu->removeDish($this);
        }
        return $this;
    }
}