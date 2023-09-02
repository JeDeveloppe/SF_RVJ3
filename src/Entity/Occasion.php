<?php

namespace App\Entity;

use App\Repository\OccasionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OccasionRepository::class)]
class Occasion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'occasions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Boite $boite = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $information = null;

    #[ORM\Column]
    private ?bool $isNew = null;

    #[ORM\ManyToOne(inversedBy: 'boxConditions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ConditionOccasion $boxCondition = null;

    #[ORM\ManyToOne(inversedBy: 'equipmentConditions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ConditionOccasion $equipmentCondition = null;

    #[ORM\ManyToOne(inversedBy: 'gameRules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ConditionOccasion $gameRule = null;

    #[ORM\Column]
    private ?bool $isOnline = null;

    #[ORM\ManyToOne(inversedBy: 'occasions')]
    #[ORM\JoinColumn(nullable: true)]
    private ?MovementOccasion $movement = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $movementTime = null;

    #[ORM\ManyToOne(inversedBy: 'occasions')]
    #[ORM\JoinColumn(nullable: true)]
    private ?MeansOfPayement $meansOfPaiement = null;

    #[ORM\Column(nullable: true)]
    private ?int $movementPrice = null;

    #[ORM\Column]
    private ?int $sellingPriceHT = null;

    #[ORM\Column(nullable: true)]
    private ?int $rvj2id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBoite(): ?Boite
    {
        return $this->boite;
    }

    public function setBoite(?Boite $boite): static
    {
        $this->boite = $boite;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(?string $information): static
    {
        $this->information = $information;

        return $this;
    }

    public function isIsNew(): ?bool
    {
        return $this->isNew;
    }

    public function setIsNew(bool $isNew): static
    {
        $this->isNew = $isNew;

        return $this;
    }

    public function getBoxCondition(): ?ConditionOccasion
    {
        return $this->boxCondition;
    }

    public function setBoxCondition(?ConditionOccasion $boxCondition): static
    {
        $this->boxCondition = $boxCondition;

        return $this;
    }

    public function getEquipmentCondition(): ?ConditionOccasion
    {
        return $this->equipmentCondition;
    }

    public function setEquipmentCondition(?ConditionOccasion $equipmentCondition): static
    {
        $this->equipmentCondition = $equipmentCondition;

        return $this;
    }

    public function getGameRule(): ?ConditionOccasion
    {
        return $this->gameRule;
    }

    public function setGameRule(?ConditionOccasion $gameRule): static
    {
        $this->gameRule = $gameRule;

        return $this;
    }

    public function isIsOnline(): ?bool
    {
        return $this->isOnline;
    }

    public function setIsOnline(bool $isOnline): static
    {
        $this->isOnline = $isOnline;

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

    public function getMovementTime(): ?\DateTimeImmutable
    {
        return $this->movementTime;
    }

    public function setMovementTime(?\DateTimeImmutable $movementTime): static
    {
        $this->movementTime = $movementTime;

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

    public function getMovementPrice(): ?int
    {
        return $this->movementPrice;
    }

    public function setMovementPrice(?int $movementPrice): static
    {
        $this->movementPrice = $movementPrice;

        return $this;
    }

    public function getSellingPriceHT(): ?int
    {
        return $this->sellingPriceHT;
    }

    public function setSellingPriceHT(int $sellingPriceHT): static
    {
        $this->sellingPriceHT = $sellingPriceHT;

        return $this;
    }

    public function getRvj2id(): ?int
    {
        return $this->rvj2id;
    }

    public function setRvj2id(int $rvj2id): static
    {
        $this->rvj2id = $rvj2id;

        return $this;
    }
}
