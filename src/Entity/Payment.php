<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Document $document = null;

    #[ORM\OneToMany(mappedBy: 'payment', targetEntity: Document::class)]
    private Collection $documents;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
    }

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

    public function __toString()
    {
        return '#'.$this->id.' par '.$this->meansOfPayment ?? 'PAS DE DOCUMENT DEFINI';
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): static
    {
        $this->document = $document;

        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setPayment($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): static
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getPayment() === $this) {
                $document->setPayment(null);
            }
        }

        return $this;
    }

}
