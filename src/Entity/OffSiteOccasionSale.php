<?php

namespace App\Entity;

use App\Repository\OffSiteOccasionSaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OffSiteOccasionSaleRepository::class)]
class OffSiteOccasionSale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'offSiteOccasionSales')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Occasion $occasion = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $movementTime = null;

    #[ORM\ManyToOne(inversedBy: 'offSiteOccasionSales')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MovementOccasion $movement = null;

    #[ORM\Column]
    private ?int $movementPrice = null;

    #[ORM\ManyToOne(inversedBy: 'offSiteOccasionSales')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MeansOfPayement $meansOfPaiement = null;

    #[ORM\ManyToOne(inversedBy: 'offSiteOccasionSales')]
    private ?User $createdBy = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'offSiteOccasionSale', targetEntity: Occasion::class)]
    private Collection $occasions;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $placeOfTransaction = null;

    #[ORM\ManyToOne(inversedBy: 'offSiteOccasionSalesBuyer')]
    private ?User $user = null;

    public function __construct()
    {
        $this->occasions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOccasion(): ?Occasion
    {
        return $this->occasion;
    }

    public function setOccasion(?Occasion $occasion): static
    {
        $this->occasion = $occasion;

        return $this;
    }

    public function getMovementTime(): ?\DateTimeImmutable
    {
        return $this->movementTime;
    }

    public function setMovementTime(?\DateTimeImmutable $movementTime): static
    {
        $this->movementTime = $movementTime;

        return $this;
    }

    public function getMovement(): ?MovementOccasion
    {
        return $this->movement;
    }

    public function setMovement(?MovementOccasion $movement): static
    {
        $this->movement = $movement;

        return $this;
    }

    public function getMovementPrice(): ?int
    {
        return $this->movementPrice;
    }

    public function setMovementPrice(int $movementPrice): static
    {
        $this->movementPrice = $movementPrice;

        return $this;
    }

    public function getMeansOfPaiement(): ?MeansOfPayement
    {
        return $this->meansOfPaiement;
    }

    public function setMeansOfPaiement(?MeansOfPayement $meansOfPaiement): static
    {
        $this->meansOfPaiement = $meansOfPaiement;

        return $this;
    }

    public function __toString()
    {
        return '#'.$this->id.' '.$this->movement;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Occasion>
     */
    public function getOccasions(): Collection
    {
        return $this->occasions;
    }

    public function addOccasion(Occasion $occasion): static
    {
        if (!$this->occasions->contains($occasion)) {
            $this->occasions->add($occasion);
            $occasion->setOffSiteOccasionSale($this);
        }

        return $this;
    }

    public function removeOccasion(Occasion $occasion): static
    {
        if ($this->occasions->removeElement($occasion)) {
            // set the owning side to null (unless already changed)
            if ($occasion->getOffSiteOccasionSale() === $this) {
                $occasion->setOffSiteOccasionSale(null);
            }
        }

        return $this;
    }

    public function getPlaceOfTransaction(): ?string
    {
        return $this->placeOfTransaction;
    }

    public function setPlaceOfTransaction(?string $placeOfTransaction): static
    {
        $this->placeOfTransaction = $placeOfTransaction;

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
