<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $timeOfTransaction = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MeansOfPayement $meansOfPayment = null;

    #[ORM\Column(length: 255)]
    private ?string $tokenPayment = null;

    #[ORM\OneToOne(mappedBy: 'payment', cascade: ['persist', 'remove'])]
    private ?Document $document = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTimeOfTransaction(): ?\DateTimeImmutable
    {
        return $this->timeOfTransaction;
    }

    public function setTimeOfTransaction(?\DateTimeImmutable $timeOfTransaction): static
    {
        $this->timeOfTransaction = $timeOfTransaction;

        return $this;
    }

    public function getMeansOfPayment(): ?MeansOfPayement
    {
        return $this->meansOfPayment;
    }

    public function setMeansOfPayment(?MeansOfPayement $meansOfPayment): static
    {
        $this->meansOfPayment = $meansOfPayment;

        return $this;
    }

    public function getTokenPayment(): ?string
    {
        return $this->tokenPayment;
    }

    public function setTokenPayment(string $tokenPayment): static
    {
        $this->tokenPayment = $tokenPayment;

        return $this;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): static
    {
        // unset the owning side of the relation if necessary
        if ($document === null && $this->document !== null) {
            $this->document->setPayment(null);
        }

        // set the owning side of the relation if necessary
        if ($document !== null && $document->getPayment() !== $this) {
            $document->setPayment($this);
        }

        $this->document = $document;

        return $this;
    }

    public function __toString()
    {
        return '#'.$this->id ?? 'PAS DE DOCUMENT DEFINI';
    }
}