<?php

namespace App\Entity;

use App\Repository\VoucherDiscountRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: VoucherDiscountRepository::class)]
class VoucherDiscount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $validUntil = null;

    #[ORM\Column]
    private ?bool $Used = null;

    #[ORM\Column]
    private ?int $numberOfKilosCollected = null;

    #[ORM\Column]
    private ?int $discountValueExcludingTax = null;

    #[ORM\ManyToOne(inversedBy: 'voucherDiscounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endOfTheCollect = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getValidUntil(): ?\DateTimeImmutable
    {
        return $this->validUntil;
    }

    public function setValidUntil(\DateTimeImmutable $validUntil): static
    {
        $this->validUntil = $validUntil;

        return $this;
    }

    public function isUsed(): ?bool
    {
        return $this->Used;
    }

    public function setUsed(bool $Used): static
    {
        $this->Used = $Used;

        return $this;
    }

    public function getNumberOfKilosCollected(): ?int
    {
        return $this->numberOfKilosCollected;
    }

    public function setNumberOfKilosCollected(int $numberOfKilosCollected): static
    {
        $this->numberOfKilosCollected = $numberOfKilosCollected;

        return $this;
    }

    public function getDiscountValueExcludingTax(): ?int
    {
        return $this->discountValueExcludingTax;
    }

    public function setDiscountValueExcludingTax(int $discountValueExcludingTax): static
    {
        $this->discountValueExcludingTax = $discountValueExcludingTax;

        return $this;
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

    public function getEndOfTheCollect(): ?\DateTimeImmutable
    {
        return $this->endOfTheCollect;
    }

    public function setEndOfTheCollect(\DateTimeImmutable $endOfTheCollect): static
    {
        $this->endOfTheCollect = $endOfTheCollect;

        return $this;
    }
}