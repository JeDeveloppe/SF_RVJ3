<?php

namespace App\Entity;

use App\Repository\PartnerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartnerRepository::class)]
class Partner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $collect = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $sells = null;

    #[ORM\Column]
    private ?bool $isAcceptDonations = null;

    #[ORM\Column(length: 255)]
    private ?string $fullUrl = null;

    #[ORM\Column]
    private ?bool $isSellsSpareParts = null;

    #[ORM\Column]
    private ?bool $isWebShop = null;

    #[ORM\Column]
    private ?bool $isOnline = null;

    #[ORM\Column]
    private ?bool $isDisplayOnCatalogueWhenSearchIsNull = null;

    #[ORM\ManyToOne(inversedBy: 'partners')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city = null;

    #[ORM\Column]
    private ?bool $isSellFullGames = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column]
    private ?int $rvj2id = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCollect(): ?string
    {
        return $this->collect;
    }

    public function setCollect(?string $collect): static
    {
        $this->collect = $collect;

        return $this;
    }

    public function getSells(): ?string
    {
        return $this->sells;
    }

    public function setSells(string $sells): static
    {
        $this->sells = $sells;

        return $this;
    }

    public function isIsAcceptDonations(): ?bool
    {
        return $this->isAcceptDonations;
    }

    public function setIsAcceptDonations(bool $isAcceptDonations): static
    {
        $this->isAcceptDonations = $isAcceptDonations;

        return $this;
    }

    public function getFullUrl(): ?string
    {
        return $this->fullUrl;
    }

    public function setFullUrl(string $fullUrl): static
    {
        $this->fullUrl = $fullUrl;

        return $this;
    }

    public function isIsSellsSpareParts(): ?bool
    {
        return $this->isSellsSpareParts;
    }

    public function setIsSellsSpareParts(bool $isSellsSpareParts): static
    {
        $this->isSellsSpareParts = $isSellsSpareParts;

        return $this;
    }

    public function isIsWebShop(): ?bool
    {
        return $this->isWebShop;
    }

    public function setIsWebShop(bool $isWebShop): static
    {
        $this->isWebShop = $isWebShop;

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

    public function isIsDisplayOnCatalogueWhenSearchIsNull(): ?bool
    {
        return $this->isDisplayOnCatalogueWhenSearchIsNull;
    }

    public function setIsDisplayOnCatalogueWhenSearchIsNull(bool $isDisplayOnCatalogueWhenSearchIsNull): static
    {
        $this->isDisplayOnCatalogueWhenSearchIsNull = $isDisplayOnCatalogueWhenSearchIsNull;

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

    public function isIsSellFullGames(): ?bool
    {
        return $this->isSellFullGames;
    }

    public function setIsSellFullGames(bool $isSellFullGames): static
    {
        $this->isSellFullGames = $isSellFullGames;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

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
