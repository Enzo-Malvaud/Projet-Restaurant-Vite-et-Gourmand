<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')] 
#[ORM\HasLifecycleCallbacks]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $delivery_datetime = null;

    #[ORM\Column]
    private ?int $number_of_persons = null;

    #[ORM\Column(length: 50)]
    private ?string $status = 'pending';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $order_price = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $delivery_price = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total_price = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;


    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Notice $notice = null;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order', orphanRemoval: true)]
    private Collection $orderItems;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
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

    public function getDeliveryDatetime(): ?\DateTimeImmutable
    {
        return $this->delivery_datetime;
    }

    public function setDeliveryDatetime(\DateTimeImmutable $delivery_datetime): static
    {
        $this->delivery_datetime = $delivery_datetime;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getNumberOfPersons(): ?int
    {
        return $this->number_of_persons;
    }

    public function setNumberOfPersons(int $number_of_persons): static
    {
        $this->number_of_persons = $number_of_persons;
        return $this;
    }

    public function getOrderPrice(): ?string
    {
        return $this->order_price;
    }

    public function setOrderPrice(string $order_price): static
    {
        $this->order_price = $order_price;
        return $this;
    }

    public function getDeliveryPrice(): ?string
    {
        return $this->delivery_price;
    }

    public function setDeliveryPrice(string $delivery_price): static
    {
        $this->delivery_price = $delivery_price;
        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->total_price;
    }

    public function setTotalPrice(string $total_price): static
    {
        $this->total_price = $total_price;
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
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setOrder($this); 
        }
        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            if ($orderItem->getOrder() === $this) { 
                $orderItem->setOrder(null);
            }
        }
        return $this;
    }
}