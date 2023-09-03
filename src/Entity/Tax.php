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

    public function __construct()
    {
        $this->legalInformation = new ArrayCollection();
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
}
