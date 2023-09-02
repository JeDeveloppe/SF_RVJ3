<?php

namespace App\Entity;

use App\Repository\OffSiteOccasionSaleRepository;
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
}
