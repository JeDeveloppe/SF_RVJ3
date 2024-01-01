<?php

namespace App\Entity;

use App\Repository\ShippingMethodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShippingMethodRepository::class)]
class ShippingMethod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'shippingMethod', targetEntity: Delivery::class)]
    private Collection $deliveries;

    #[ORM\Column]
    private ?bool $isActivedInCart = null;

    #[ORM\Column(length: 255)]
    private ?string $price = null;

    #[ORM\OneToMany(mappedBy: 'shippingMethod', targetEntity: Documentsending::class)]
    private Collection $documentsendings;

    public function __construct()
    {
        $this->deliveries = new ArrayCollection();
        $this->documentsendings = new ArrayCollection();
    }

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

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection<int, Delivery>
     */
    public function getDeliveries(): Collection
    {
        return $this->deliveries;
    }

    public function addDelivery(Delivery $delivery): static
    {
        if (!$this->deliveries->contains($delivery)) {
            $this->deliveries->add($delivery);
            $delivery->setShippingMethod($this);
        }

        return $this;
    }

    public function removeDelivery(Delivery $delivery): static
    {
        if ($this->deliveries->removeElement($delivery)) {
            // set the owning side to null (unless already changed)
            if ($delivery->getShippingMethod() === $this) {
                $delivery->setShippingMethod(null);
            }
        }

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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Documentsending>
     */
    public function getDocumentsendings(): Collection
    {
        return $this->documentsendings;
    }

    public function addDocumentsending(Documentsending $documentsending): static
    {
        if (!$this->documentsendings->contains($documentsending)) {
            $this->documentsendings->add($documentsending);
            $documentsending->setShippingMethod($this);
        }

        return $this;
    }

    public function removeDocumentsending(Documentsending $documentsending): static
    {
        if ($this->documentsendings->removeElement($documentsending)) {
            // set the owning side to null (unless already changed)
            if ($documentsending->getShippingMethod() === $this) {
                $documentsending->setShippingMethod(null);
            }
        }

        return $this;
    }
}
