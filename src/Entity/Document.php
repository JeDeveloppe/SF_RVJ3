<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
class Document
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\Column(nullable: true)]
    private ?int $rvj2id = null;

    #[ORM\Column]
    private ?string $quoteNumber = null;

    #[ORM\Column(nullable: true)]
    private ?string $billNumber = null;

    #[ORM\Column]
    private ?int $totalExcludingTax = null;

    #[ORM\Column]
    private ?int $totalWithTax = null;

    #[ORM\Column]
    private ?int $deliveryPriceExcludingTax = null;

    #[ORM\Column]
    private ?bool $isQuoteReminder = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endOfQuoteValidation = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $timeOfSendingQuote = null;

    #[ORM\Column]
    private ?bool $isDeleteByUser = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tax $taxRate = null;

    #[ORM\Column(nullable: true)]
    private ?int $cost = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $deliveryAddress = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $billingAddress = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ShippingMethod $sendingMethod = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DocumentStatus $documentStatus = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tokenPayment = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'document', targetEntity: DocumentLine::class)]
    private Collection $documentLines;

    #[ORM\OneToMany(mappedBy: 'document', targetEntity: Payment::class)]
    private Collection $payments;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    private ?Payment $payment = null;

    public function __construct()
    {
        $this->documentLines = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getRvj2id(): ?int
    {
        return $this->rvj2id;
    }

    public function setRvj2id(?int $rvj2id): static
    {
        $this->rvj2id = $rvj2id;

        return $this;
    }

    public function getQuoteNumber(): ?string
    {
        return $this->quoteNumber;
    }

    public function setQuoteNumber(string $quoteNumber): static
    {
        $this->quoteNumber = $quoteNumber;

        return $this;
    }

    public function getBillNumber(): ?string
    {
        return $this->billNumber;
    }

    public function setBillNumber(?string $billNumber): static
    {
        $this->billNumber = $billNumber;

        return $this;
    }

    public function getTotalExcludingTax(): ?int
    {
        return $this->totalExcludingTax;
    }

    public function setTotalExcludingTax(int $totalExcludingTax): static
    {
        $this->totalExcludingTax = $totalExcludingTax;

        return $this;
    }

    public function getTotalWithTax(): ?int
    {
        return $this->totalWithTax;
    }

    public function setTotalWithTax(int $totalWithTax): static
    {
        $this->totalWithTax = $totalWithTax;

        return $this;
    }

    public function getDeliveryPriceExcludingTax(): ?int
    {
        return $this->deliveryPriceExcludingTax;
    }

    public function setDeliveryPriceExcludingTax(int $deliveryPriceExcludingTax): static
    {
        $this->deliveryPriceExcludingTax = $deliveryPriceExcludingTax;

        return $this;
    }

    public function isIsQuoteReminder(): ?bool
    {
        return $this->isQuoteReminder;
    }

    public function setIsQuoteReminder(bool $isQuoteReminder): static
    {
        $this->isQuoteReminder = $isQuoteReminder;

        return $this;
    }

    public function getEndOfQuoteValidation(): ?\DateTimeImmutable
    {
        return $this->endOfQuoteValidation;
    }

    public function setEndOfQuoteValidation(\DateTimeImmutable $endOfQuoteValidation): static
    {
        $this->endOfQuoteValidation = $endOfQuoteValidation;

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

    public function getTimeOfSendingQuote(): ?\DateTimeImmutable
    {
        return $this->timeOfSendingQuote;
    }

    public function setTimeOfSendingQuote(\DateTimeImmutable $timeOfSendingQuote): static
    {
        $this->timeOfSendingQuote = $timeOfSendingQuote;

        return $this;
    }

    public function isIsDeleteByUser(): ?bool
    {
        return $this->isDeleteByUser;
    }

    public function setIsDeleteByUser(bool $isDeleteByUser): static
    {
        $this->isDeleteByUser = $isDeleteByUser;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getTaxRate(): ?Tax
    {
        return $this->taxRate;
    }

    public function setTaxRate(?Tax $taxRate): static
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(?int $cost): static
    {
        $this->cost = $cost;

        return $this;
    }

    public function getDeliveryAddress(): ?string
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(string $deliveryAddress): static
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    public function getBillingAddress(): ?string
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(string $billingAddress): static
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getSendingMethod(): ?ShippingMethod
    {
        return $this->sendingMethod;
    }

    public function setSendingMethod(?ShippingMethod $sendingMethod): static
    {
        $this->sendingMethod = $sendingMethod;

        return $this;
    }

    public function getDocumentStatus(): ?DocumentStatus
    {
        return $this->documentStatus;
    }

    public function setDocumentStatus(?DocumentStatus $documentStatus): static
    {
        $this->documentStatus = $documentStatus;

        return $this;
    }

    public function getTokenPayment(): ?string
    {
        return $this->tokenPayment;
    }

    public function setTokenPayment(?string $tokenPayment): static
    {
        $this->tokenPayment = $tokenPayment;

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

    public function __toString()
    {
        return $this->billNumber ?? $this->quoteNumber;
    }

    /**
     * @return Collection<int, DocumentLine>
     */
    public function getDocumentLines(): Collection
    {
        return $this->documentLines;
    }

    public function addDocumentLine(DocumentLine $documentLine): static
    {
        if (!$this->documentLines->contains($documentLine)) {
            $this->documentLines->add($documentLine);
            $documentLine->setDocument($this);
        }

        return $this;
    }

    public function removeDocumentLine(DocumentLine $documentLine): static
    {
        if ($this->documentLines->removeElement($documentLine)) {
            // set the owning side to null (unless already changed)
            if ($documentLine->getDocument() === $this) {
                $documentLine->setDocument(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): static
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setDocument($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getDocument() === $this) {
                $payment->setDocument(null);
            }
        }

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): static
    {
        $this->payment = $payment;

        return $this;
    }
}
