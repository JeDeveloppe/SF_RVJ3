<?php

namespace App\Entity;

use App\Repository\DocumentsendingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentsendingRepository::class)]
class Documentsending
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'documentsending', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Document $document = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sendingNumber = null;

    #[ORM\ManyToOne(inversedBy: 'documentsendings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ShippingMethod $shippingMethod = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $sendingAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(Document $document): static
    {
        $this->document = $document;

        return $this;
    }

    public function getSendingNumber(): ?string
    {
        return $this->sendingNumber;
    }

    public function setSendingNumber(?string $sendingNumber): static
    {
        $this->sendingNumber = $sendingNumber;

        return $this;
    }

    public function getShippingMethod(): ?ShippingMethod
    {
        return $this->shippingMethod;
    }

    public function setShippingMethod(?ShippingMethod $shippingMethod): static
    {
        $this->shippingMethod = $shippingMethod;

        return $this;
    }

    public function getSendingAt(): ?\DateTimeImmutable
    {
        return $this->sendingAt;
    }

    public function setSendingAt(?\DateTimeImmutable $sendingAt): static
    {
        $this->sendingAt = $sendingAt;

        return $this;
    }
}
