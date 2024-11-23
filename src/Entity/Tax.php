<?php

namespace App\Entity;

use App\Repository\TaxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaxRepository::class)]
class Tax
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $value = null;

    #[ORM\OneToMany(mappedBy: 'tax', targetEntity: LegalInformation::class)]
    private Collection $legalInformation;

    #[ORM\OneToMany(mappedBy: 'taxRate', targetEntity: Document::class)]
    private Collection $documents;

    public function __construct()
    {
        $this->legalInformation = new ArrayCollection();
        $this->documents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return Collection<int, LegalInformation>
     */
    public function getLegalInformation(): Collection
    {
        return $this->legalInformation;
    }

    public function addLegalInformation(LegalInformation $legalInformation): static
    {
        if (!$this->legalInformation->contains($legalInformation)) {
            $this->legalInformation->add($legalInformation);
            $legalInformation->setTax($this);
        }

        return $this;
    }

    public function removeLegalInformation(LegalInformation $legalInformation): static
    {
        if ($this->legalInformation->removeElement($legalInformation)) {
            // set the owning side to null (unless already changed)
            if ($legalInformation->getTax() === $this) {
                $legalInformation->setTax(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->value.'%';
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
            $document->setTaxRate($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): static
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getTaxRate() === $this) {
                $document->setTaxRate(null);
            }
        }

        return $this;
    }
}
