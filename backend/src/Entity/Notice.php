<?php

namespace App\Entity;

use App\Repository\NoticeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: NoticeRepository::class)]
#[ORM\HasLifecycleCallbacks] 
class Notice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['notice:read'])]
    private ?int $id = null;

    /**
     * ✅ CORRIGÉ: Ajout des groupes
     */
    #[ORM\Column(length: 50)]
    #[Groups(['notice:read', 'notice:write'])]
    private ?string $title = null;

    /**
     * ✅ CORRIGÉ: Ajout des groupes
     */
    #[ORM\Column]
    #[Groups(['notice:read', 'notice:write'])]
    private ?int $note = null; 

    /**
     * ✅ CORRIGÉ: Ajout des groupes
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['notice:read', 'notice:write'])]
    private ?string $description = null;

    /**
     * ✅ CORRIGÉ: Ajout des groupes
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['notice:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * ✅ CORRIGÉ: Ajout des groupes
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['notice:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Rental>
     * ⚠️ PAS DE GROUPES: Back-reference, à utiliser avec prudence
     * (risque de sérialiser des données circulaires)
     */
    #[ORM\OneToMany(targetEntity: Rental::class, mappedBy: 'notice')]
    private Collection $rentals;

    /**
     * @var Collection<int, Order>
     * ⚠️ PAS DE GROUPES: Back-reference, à utiliser avec prudence
     * (risque de sérialiser des données circulaires)
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'notice')]
    private Collection $orders;

    public function __construct()
    {
        $this->rentals = new ArrayCollection();
        $this->orders = new ArrayCollection();
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
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

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): static
    {
        $this->note = $note;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * ✅ AJOUT: Setter pour createdAt
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * ✅ AJOUT: Setter pour updatedAt
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, Rental>
     */
    public function getRentals(): Collection
    {
        return $this->rentals;
    }

    public function addRental(Rental $rental): static
    {
        if (!$this->rentals->contains($rental)) {
            $this->rentals->add($rental);
            $rental->setNotice($this);
        }
        return $this;
    }

    public function removeRental(Rental $rental): static
    {
        if ($this->rentals->removeElement($rental)) {
            if ($rental->getNotice() === $this) {
                $rental->setNotice(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setNotice($this);
        }
        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            if ($order->getNotice() === $this) {
                $order->setNotice(null);
            }
        }
        return $this;
    }
}