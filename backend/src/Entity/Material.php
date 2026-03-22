<?php

namespace App\Entity;

use App\Repository\MaterialRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MaterialRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Material
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['material:read'])]
    private ?int $id = null;

    /**
     * ✅ CORRIGÉ: Ajout des groupes
     */
    #[ORM\Column(length: 50, unique: true)]
    #[Groups(['material:read', 'material:write'])]
    private ?string $name = null;

    /**
     * ✅ CORRIGÉ: Ajout des groupes
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['material:read', 'material:write'])]
    private ?string $description = null;

    /**
     * ✅ CORRIGÉ: Type float au lieu de string (DECIMAL)
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['material:read', 'material:write'])]
    private ?float $daily_rental_price = null;

    /**
     * ✅ CORRIGÉ: Ajout des groupes
     */
    #[ORM\Column]
    #[Groups(['material:read', 'material:write'])]
    private ?int $total_quantity = null;

    /**
     * ✅ CORRIGÉ: Ajout des groupes
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['material:read', 'material:write'])]
    private ?string $picture = null;

    /**
     * ✅ CORRIGÉ: Type float au lieu de string (DECIMAL)
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['material:read', 'material:write'])]
    private ?float $caution = null;

    /**
     * ✅ CORRIGÉ: Ajout des groupes
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['material:read', 'material:write'])]
    private ?string $rental_condition = null;

    /**
     * ✅ CORRIGÉ: Ajout des groupes
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['material:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * ✅ CORRIGÉ: Ajout des groupes
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['material:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * ⚠️ PAS DE GROUPES: Back-reference
     */
    #[ORM\OneToMany(targetEntity: MaterialRental::class, mappedBy: 'material')]
    private Collection $materialRentals;

    public function __construct()
    {
        $this->materialRentals = new ArrayCollection();
    }

    /**
     * ✅ CORRECT: PrePersist avec vérification null
     */
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    /**
     * ✅ CORRECT: PreUpdate automatique
     */
    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

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

    /**
     * ✅ CORRIGÉ: Retourne float au lieu de string
     */
    public function getDailyRentalPrice(): ?float
    {
        return $this->daily_rental_price;
    }

    /**
     * ✅ CORRIGÉ: Accepte float au lieu de string
     */
    public function setDailyRentalPrice(float $daily_rental_price): static
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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;
        return $this;
    }

    /**
     * ✅ CORRIGÉ: Retourne float au lieu de string
     */
    public function getCaution(): ?float
    {
        return $this->caution;
    }

    /**
     * ✅ CORRIGÉ: Accepte float au lieu de string
     */
    public function setCaution(float $caution): static
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

    /**
     * @return Collection<int, MaterialRental>
     */
    public function getMaterialRentals(): Collection
    {
        return $this->materialRentals;
    }

    /**
     * ✅ AJOUT: Ajouter une location de matériel
     */
    public function addMaterialRental(MaterialRental $materialRental): static
    {
        if (!$this->materialRentals->contains($materialRental)) {
            $this->materialRentals->add($materialRental);
            $materialRental->setMaterial($this);
        }
        return $this;
    }

    /**
     * ✅ AJOUT: Retirer une location de matériel
     */
    public function removeMaterialRental(MaterialRental $materialRental): static
    {
        if ($this->materialRentals->removeElement($materialRental)) {
            if ($materialRental->getMaterial() === $this) {
                $materialRental->setMaterial(null);
            }
        }
        return $this;
    }
}