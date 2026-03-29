<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
// use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // #[Assert\NotBlank]
    #[Assert\Length(min: 8, minMessage: 'title must be at least {{ limit }} characters long')]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    // #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^([+]{1})?[0-9]+(\.[0-9]+)?$/',
        message: 'price must be positiv decimal'
    )]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\ManyToOne]
    private ?Category $category = null;

    // public static function loadValidatorMetadata(ClassMetadata $metadata): void
    // {
    //     $metadata->addPropertyConstraint('name', new Assert\NotBlank());
    // }

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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
