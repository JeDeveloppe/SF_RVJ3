<?php

namespace App\Entity;

use App\Repository\MeansOfPayementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeansOfPayementRepository::class)]
class MeansOfPayement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'meansOfPaiement', targetEntity: OffSiteOccasionSale::class)]
    private Collection $offSiteOccasionSales;

    #[ORM\OneToMany(mappedBy: 'meansOfPayment', targetEntity: Payment::class)]
    private Collection $payments;

    public function __construct()
    {
        $this->offSiteOccasionSales = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

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

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection<int, OffSiteOccasionSale>
     */
    public function getOffSiteOccasionSales(): Collection
    {
        return $this->offSiteOccasionSales;
    }

    public function addOffSiteOccasionSale(OffSiteOccasionSale $offSiteOccasionSale): static
    {
        if (!$this->offSiteOccasionSales->contains($offSiteOccasionSale)) {
            $this->offSiteOccasionSales->add($offSiteOccasionSale);
            $offSiteOccasionSale->setMeansOfPaiement($this);
        }

        return $this;
    }

    public function removeOffSiteOccasionSale(OffSiteOccasionSale $offSiteOccasionSale): static
    {
        if ($this->offSiteOccasionSales->removeElement($offSiteOccasionSale)) {
            // set the owning side to null (unless already changed)
            if ($offSiteOccasionSale->getMeansOfPaiement() === $this) {
                $offSiteOccasionSale->setMeansOfPaiement(null);
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
            $payment->setMeansOfPayment($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): static
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getMeansOfPayment() === $this) {
                $payment->setMeansOfPayment(null);
            }
        }

        return $this;
    }
}
