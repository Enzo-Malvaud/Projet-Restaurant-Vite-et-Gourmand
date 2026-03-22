<?php

namespace App\Entity;

use App\Repository\RentalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: RentalRepository::class)]
#[ORM\HasLifecycleCallbacks] 
class Rental
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['rental:read'])]
    private ?int $id = null;
    
    #[ORM\Column]
    #[Groups(['rental:read', 'rental:write'])]
    private ?string $title = null;
    
    #[ORM\Column]
    #[Groups(['rental:read', 'rental:write'])]
    private ?\DateTimeImmutable $dateTimeOfRendering = null;

    #[ORM\Column(length: 50)]
    #[Groups(['rental:read', 'rental:write'])]
    private ?string $status = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['rental:read', 'rental:write'])]
    private ?string $rentalPrice = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['rental:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['rental:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'rentals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'rentals')]
    private ?Notice $notice = null;

    /**
     * @var Collection<int, MaterialRental>
     */
    #[ORM\OneToMany(targetEntity: MaterialRental::class, mappedBy: 'rental', orphanRemoval: true)]
    private Collection $materialRentals;

    public function __construct()
    {
        $this->status = 'pending';
        $this->materialRentals = new ArrayCollection();
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDateTimeOfRendering(): ?\DateTimeImmutable
    {
        return $this->dateTimeOfRendering;
    }

    public function setDateTimeOfRendering(\DateTimeImmutable $dateTimeOfRendering): static
    {
        $this->dateTimeOfRendering = $dateTimeOfRendering;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getRentalPrice(): ?string
    {
        return $this->rentalPrice;
    }

    public function setRentalPrice(string $rentalPrice): static
    {
        $this->rentalPrice = $rentalPrice;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getNotice(): ?Notice
    {
        return $this->notice;
    }

    public function setNotice(?Notice $notice): static
    {
        $this->notice = $notice;
        return $this;
    }

    /**
     * @return Collection<int, MaterialRental>
     */
    public function getMaterialRentals(): Collection
    {
        return $this->materialRentals;
    }

    public function addMaterialRental(MaterialRental $materialRental): static
    {
        if (!$this->materialRentals->contains($materialRental)) {
            $this->materialRentals->add($materialRental);
            $materialRental->setRental($this);
        }
        return $this;
    }

    public function removeMaterialRental(MaterialRental $materialRental): static
    {
        if ($this->materialRentals->removeElement($materialRental)) {
            if ($materialRental->getRental() === $this) {
                $materialRental->setRental(null);
            }
        }
        return $this;
    }
}