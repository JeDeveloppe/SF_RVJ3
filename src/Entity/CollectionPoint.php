<?php

namespace App\Entity;

use App\Repository\CollectionPointRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollectionPointRepository::class)]
class CollectionPoint
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $organization = null;

    #[ORM\Column(length: 255)]
    private ?string $street = null;

    #[ORM\ManyToOne(inversedBy: 'collectionPoints')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city = null;

    #[ORM\Column]
    private ?bool $isActivedInCart = null;

    #[ORM\ManyToOne(inversedBy: 'collectionPoints')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ShippingMethod $shippingmethod = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getOrganization(): ?string
    {
        return $this->organization;
    }

    public function setOrganization(?string $organization): static
    {
        $this->organization = $organization;

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
        return $this->organization.' '.$this->firstname.' '.$this->lastname.' '.$this->street.' '.$this->city;
    }

    public function getShippingmethod(): ?ShippingMethod
    {
        return $this->shippingmethod;
    }

    public function setShippingmethod(?ShippingMethod $shippingmethod): static
    {
        $this->shippingmethod = $shippingmethod;

        return $this;
    }
}
