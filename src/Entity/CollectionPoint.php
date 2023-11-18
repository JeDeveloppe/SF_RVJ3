<?php

namespace App\Entity;

use App\Repository\CollectionPointRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollectionPointRepository::class)]
class CollectionPoint
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $street = null;

    #[ORM\ManyToOne(inversedBy: 'collectionPoints')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city = null;

    #[ORM\Column]
    private ?bool $isActivedInCart = null;

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

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function isIsActivedInCart(): ?bool
    {
        return $this->isActivedInCart;
    }

    public function setIsActivedInCart(bool $isActivedInCart): static
    {
        $this->isActivedInCart = $isActivedInCart;

        return $this;
    }

    public function __toString()
    {
        return $this->name.' '.$this->street.' '.$this->city;
    }
}
