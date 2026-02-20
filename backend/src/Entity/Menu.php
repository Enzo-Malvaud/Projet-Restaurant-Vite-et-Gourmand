<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title_menu = null;

    #[ORM\Column]
    private ?int $minimum_number_of_persons = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price_menu = null;


    #[ORM\Column(type: Types::TEXT)]
    private ?string $list_menu = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $remaining_quantity = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $precaution_menu = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $storage_precautions = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $price_per_person = null;

    
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Dish>
     */
    #[ORM\ManyToMany(targetEntity: Dish::class)]
    private Collection $id_dish;

    #[ORM\ManyToOne]
    private ?Regime $regime = null;

    /**
     * @var Collection<int, ThemeMenu>
     */
    #[ORM\ManyToMany(targetEntity: ThemeMenu::class)]
    private Collection $id_theme;

    public function __construct()
    {
        $this->id_dish = new ArrayCollection();
        $this->id_theme = new ArrayCollection();
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

    public function getPriceMenu(): ?string
    {
        return $this->price_menu;
    }

    public function setPriceMenu(string $price_menu): static
    {
        $this->price_menu = $price_menu;

        return $this;
    }

    public function getListMenu(): ?string
    {
        return $this->list_menu;
    }

    public function setListMenu(string $list_menu): static
    {
        $this->list_menu = $list_menu;

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

    public function getPricePerPerson(): ?string
    {
        return $this->price_per_person;
    }

    public function setPricePerPerson(string $price_per_person): static
    {
        $this->price_per_person = $price_per_person;

        return $this;
    }

    /**
     * @return Collection<int, Dish>
     */
    public function getIdDish(): Collection
    {
        return $this->id_dish;
    }

    public function addIdDish(Dish $idDish): static
    {
        if (!$this->id_dish->contains($idDish)) {
            $this->id_dish->add($idDish);
        }

        return $this;
    }

    public function removeIdDish(Dish $idDish): static
    {
        $this->id_dish->removeElement($idDish);

        return $this;
    }

    public function getRegime(): ?Regime
    {
        return $this->regime;
    }

    public function setRegime(?Regime $regime): static
    {
        $this->regime = $regime;

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
     * @return Collection<int, ThemeMenu>
     */
    public function getIdTheme(): Collection
    {
        return $this->id_theme;
    }

    public function addIdTheme(ThemeMenu $idTheme): static
    {
        if (!$this->id_theme->contains($idTheme)) {
            $this->id_theme->add($idTheme);
        }

        return $this;
    }

    public function removeIdTheme(ThemeMenu $idTheme): static
    {
        $this->id_theme->removeElement($idTheme);

        return $this;
    }
}
