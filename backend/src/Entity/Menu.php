<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
#[ORM\HasLifecycleCallbacks] 
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['menu:read'])]
    private ?int $id = null;

  
    #[ORM\Column(length: 255)]
    #[Groups(['menu:read', 'menu:write'])]
    private ?string $title_menu = null;


    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['menu:read', 'menu:write'])]
    private ?string $description = null;

  
    #[ORM\Column]
    #[Groups(['menu:read', 'menu:write'])]
    private ?int $minimum_number_of_persons = null;

  
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['menu:read', 'menu:write'])]
    private ?float $price_menu = null;

  
    #[ORM\Column]
    #[Groups(['menu:read', 'menu:write'])]
    private ?int $remaining_quantity = null;

  
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['menu:read', 'menu:write'])]
    private ?string $precaution_menu = null;


    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['menu:read', 'menu:write'])]
    private ?string $storage_precautions = null;

  
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['menu:read'])]
    private ?\DateTimeImmutable $createdAt = null;


    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['menu:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Dish>

     */
    #[ORM\ManyToMany(targetEntity: Dish::class, inversedBy: 'menus')]
    #[Groups(['menu:read'])]
    private Collection $dishs;

    /**
     * @var Collection<int, Regime>

     */
    #[ORM\ManyToMany(targetEntity: Regime::class, inversedBy: 'menus')]
    #[Groups(['menu:read'])]
    private Collection $diets;

    /**
     * @var Collection<int, ThemeMenu>

     */
    #[ORM\ManyToMany(targetEntity: ThemeMenu::class, inversedBy: 'menus')]
    #[Groups(['menu:read'])]
    private Collection $themes;

    public function __construct()
    {
        $this->dishs = new ArrayCollection();
        $this->diets = new ArrayCollection();
        $this->themes = new ArrayCollection();
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

    public function getTitleMenu(): ?string
    {
        return $this->title_menu;
    }

    public function setTitleMenu(string $title_menu): static
    {
        $this->title_menu = $title_menu;
        return $this;
    }

    public function getMinimumNumberOfPersons(): ?int
    {
        return $this->minimum_number_of_persons;
    }

    public function setMinimumNumberOfPersons(int $minimum_number_of_persons): static
    {
        $this->minimum_number_of_persons = $minimum_number_of_persons;
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

    public function getRemainingQuantity(): ?int
    {
        return $this->remaining_quantity;
    }

    public function setRemainingQuantity(int $remaining_quantity): static
    {
        $this->remaining_quantity = $remaining_quantity;
        return $this;
    }

    public function getPrecautionMenu(): ?string
    {
        return $this->precaution_menu;
    }

    public function setPrecautionMenu(?string $precaution_menu): static
    {
        $this->precaution_menu = $precaution_menu;
        return $this;
    }

    public function getStoragePrecautions(): ?string
    {
        return $this->storage_precautions;
    }

    public function setStoragePrecautions(?string $storage_precautions): static
    {
        $this->storage_precautions = $storage_precautions;
        return $this;
    }

    public function getPriceMenu(): ?float
    {
        return $this->price_menu;
    }


    public function setPriceMenu(float $price_menu): static
    {
        $this->price_menu = $price_menu;
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
     * @return Collection<int, Dish>
     */
    public function getDishs(): Collection
    {
        return $this->dishs;
    }

    public function addDish(Dish $dish): static
    {
        if (!$this->dishs->contains($dish)) {
            $this->dishs->add($dish);
        }
        return $this;
    }

    public function removeDish(Dish $dish): static
    {
        $this->dishs->removeElement($dish);
        return $this;
    }

    /**
     * @return Collection<int, Regime>
     */
    public function getDiets(): Collection
    {
        return $this->diets;
    }

    public function addDiet(Regime $diet): static
    {
        if (!$this->diets->contains($diet)) {
            $this->diets->add($diet);
        }
        return $this;
    }

    public function removeDiet(Regime $diet): static
    {
        $this->diets->removeElement($diet);
        return $this;
    }

    /**
     * @return Collection<int, ThemeMenu>
     */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    public function addTheme(ThemeMenu $theme): static
    {
        if (!$this->themes->contains($theme)) {
            $this->themes->add($theme);
        }
        return $this;
    }

    public function removeTheme(ThemeMenu $theme): static
    {
        $this->themes->removeElement($theme);
        return $this;
    }
}