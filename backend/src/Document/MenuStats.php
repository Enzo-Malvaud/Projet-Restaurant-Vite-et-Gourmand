<?php
// src/Document/MenuStats.php
namespace App\Document;

// 1. Correction de l'import : on utilise Annotations pour les attributs ODM
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "menu_stats")]
class MenuStats
{
    #[MongoDB\Id]
    private ?string $id = null;

    #[MongoDB\Field(type: "int")]
    private ?int $menuId = null;

    #[MongoDB\Field(type: "string")]
    private ?string $menuName = null;

    #[MongoDB\Field(type: "float")]
    private ?float $price = null;

    #[MongoDB\Field(type: "date_immutable")]
    private ?\DateTimeImmutable $createdAt =null;

    #[MongoDB\Field(type: "date_immutable")]
    private ?\DateTimeImmutable $updatedAt =null;

    public function __construct()
    {

        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();

    }

    // --- GETTERS ---

    public function getId(): ?string { return $this->id; }
    public function getMenuId(): ?int { return $this->menuId; }
    public function getMenuName(): ?string { return $this->menuName; }
    public function getPrice(): ?float { return $this->price; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    // --- SETTERS ---

    public function setMenuId(int $menuId): self 
    { 
        $this->menuId = $menuId; 
        return $this; 
    }
    
    public function setMenuName(string $menuName): self 
    { 
        $this->menuName = $menuName; 
        return $this; 
    }
    
    public function setPrice(float $price): self 
    { 
        $this->price = $price; 
        return $this; 
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

}