<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 36)]
    private ?string $uuid = null;

    #[ORM\Column(length: 64)]
    private ?string $title = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Food>
     */
    #[ORM\ManyToMany(targetEntity: Food::class, inversedBy: 'categoriesId')]
    private Collection $FoodId;

    /**
     * @var Collection<int, Menu>
     */
    #[ORM\ManyToMany(targetEntity: Menu::class, mappedBy: 'CategoryId')]
    private Collection $MenuId;

    public function __construct()
    {
        $this->FoodId = new ArrayCollection();
        $this->MenuId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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
     * @return Collection<int, Food>
     */
    public function getFoodId(): Collection
    {
        return $this->FoodId;
    }

    public function addFoodId(Food $foodId): static
    {
        if (!$this->FoodId->contains($foodId)) {
            $this->FoodId->add($foodId);
        }

        return $this;
    }

    public function removeFoodId(Food $foodId): static
    {
        $this->FoodId->removeElement($foodId);

        return $this;
    }

    /**
     * @return Collection<int, Menu>
     */
    public function getMenuId(): Collection
    {
        return $this->MenuId;
    }

    public function addMenuId(Menu $menuId): static
    {
        if (!$this->MenuId->contains($menuId)) {
            $this->MenuId->add($menuId);
            $menuId->addCategoryId($this);
        }

        return $this;
    }

    public function removeMenuId(Menu $menuId): static
    {
        if ($this->MenuId->removeElement($menuId)) {
            $menuId->removeCategoryId($this);
        }

        return $this;
    }
}
