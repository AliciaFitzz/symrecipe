<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
#[UniqueEntity('name')] // pour dire qu'il ne peut pas y avoir plusieurs fois le même nom
class Ingredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // contrainte qui signifie qu'on veux 2 caractère min et 50 max
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le nom doit avoir minimum 2 caractères',
        maxMessage: 'Le nom doit avoir maximum 50 caractères',
    )]
    // pour pas que les données soient null ou string vide
    #[Assert\NotBlank]
    #[ORM\Column(length: 50)]
    private ?string $name = null;

    // Positive indique qu'on veut un nombre supérieur à 0, LessThan qu'on veux moins de 200    
    #[Assert\Positive]
    #[Assert\LessThan(200)]
    #[Assert\NotNull]
    #[ORM\Column]
    private ?float $price = null;

    #[Assert\NotNull]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        // Pour dire qu'à chaque fois qu'un indrédient va se construire, il aura la date actuelle
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
