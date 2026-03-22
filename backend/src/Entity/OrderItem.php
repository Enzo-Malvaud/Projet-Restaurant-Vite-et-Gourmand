<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
#[ORM\HasLifecycleCallbacks]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['orderItem:read'])]
    private ?int $id = null;

    /**
     * ✅ CORRIGÉ: Ajout des groupes
     */
    #[ORM\Column]
    #[Groups(['orderItem:read', 'orderItem:write'])]
    private ?int $quantity = null; 

    /**
     * ✅ CORRIGÉ: Type float au lieu de string (DECIMAL stocke en float)
     */
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['orderItem:read'])]
    private ?float $price_unit = null;

    /**
     * ✅ CORRIGÉ: Client envoie l'ID du menu en write
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['orderItem:write'])]
    private ?Menu $menu = null;

    /**
     * ❌ PAS DE GROUPES: C'est une back-reference, ne jamais sérialiser
     */
    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;
    
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['orderItem:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * ✅ CORRIGÉ: Retourne un float
     */
    public function getPriceUnit(): ?float
    {
        return $this->price_unit;
    }

    /**
     * ✅ CORRIGÉ: Accepte un float
     */
    public function setPriceUnit(float $price_unit): static
    {
        $this->price_unit = $price_unit;
        return $this;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): static
    {
        $this->menu = $menu;
        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): static
    {
        $this->order = $order;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * ✅ CORRIGÉ: Ajout du setter
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}