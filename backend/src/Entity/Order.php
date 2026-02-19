<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $order_date = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $delivery_date = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $delivery_time = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $order_price = null;

    #[ORM\Column]
    private ?int $number_of_persons = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $delivery_price = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $total_price = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $date_modified = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Adresse $adresse = null;

    /**
     * @var Collection<int, Menu>
     */
    #[ORM\ManyToMany(targetEntity: Menu::class)]
    private Collection $id_menu;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?User $user = null;

    public function __construct()
    {
        $this->id_menu = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderDate(): ?\DateTimeImmutable
    {
        return $this->order_date;
    }

    public function setOrderDate(\DateTimeImmutable $order_date): static
    {
        $this->order_date = $order_date;

        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeImmutable
    {
        return $this->delivery_date;
    }

    public function setDeliveryDate(\DateTimeImmutable $delivery_date): static
    {
        $this->delivery_date = $delivery_date;

        return $this;
    }

        public function getDeliveryTime(): ?\DateTimeImmutable
    {
        return $this->delivery_time;
    }

    public function setDeliveryTime(?\DateTimeImmutable $delivery_time): static
    {
        $this->delivery_time = $delivery_time;

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

    public function getNumberOfPersons(): ?int
    {
        return $this->number_of_persons;
    }

    public function setNumberOfPersons(int $number_of_persons): static
    {
        $this->number_of_persons = $number_of_persons;

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

    public function getDateModified(): ?\DateTimeImmutable
    {
        return $this->date_modified;
    }

    public function setDateModified(?\DateTimeImmutable $date_modified): static
    {
        $this->date_modified = $date_modified;

        return $this;
    }

    public function getAdresse(): ?Adresse
    {
        return $this->adresse;
    }

    public function setAdresse(?Adresse $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return Collection<int, Menu>
     */
    public function getIdMenu(): Collection
    {
        return $this->id_menu;
    }

    public function addIdMenu(Menu $idMenu): static
    {
        if (!$this->id_menu->contains($idMenu)) {
            $this->id_menu->add($idMenu);
        }

        return $this;
    }

    public function removeIdMenu(Menu $idMenu): static
    {
        $this->id_menu->removeElement($idMenu);

        return $this;
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

    
}
