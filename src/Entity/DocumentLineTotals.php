<?php

namespace App\Entity;

use App\Repository\DocumentLineTotalsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentLineTotalsRepository::class)]
class DocumentLineTotals
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $itemsWeigth = null;

    #[ORM\Column]
    private ?int $itemsPriceWithoutTax = null;

    #[ORM\Column]
    private ?int $occasionsWeigth = null;

    #[ORM\Column]
    private ?int $occasionsPriceWithoutTax = null;

    #[ORM\Column]
    private ?int $boitesWeigth = null;

    #[ORM\Column]
    private ?int $boitesPriceWithoutTax = null;

    #[ORM\OneToOne(inversedBy: 'documentLineTotals', cascade: ['persist', 'remove'])]
    private ?Document $document = null;

    #[ORM\Column]
    private ?int $discountonpurchase = null;

    #[ORM\Column(nullable: true)]
    private ?int $discountonpurchaseinpurcentage = null;

    #[ORM\Column]
    private ?int $voucherDiscountValueUsed = null;

    #[ORM\ManyToMany(targetEntity: VoucherDiscount::class, mappedBy: 'documentLineTotals')]
    private Collection $voucherDiscounts;

    public function __construct()
    {
        $this->voucherDiscounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItemsWeigth(): ?int
    {
        return $this->itemsWeigth;
    }

    public function setItemsWeigth(int $itemsWeigth): static
    {
        $this->itemsWeigth = $itemsWeigth;

        return $this;
    }

    public function getItemsPriceWithoutTax(): ?int
    {
        return $this->itemsPriceWithoutTax;
    }

    public function setItemsPriceWithoutTax(int $itemsPriceWithoutTax): static
    {
        $this->itemsPriceWithoutTax = $itemsPriceWithoutTax;

        return $this;
    }

    public function getOccasionsWeigth(): ?int
    {
        return $this->occasionsWeigth;
    }

    public function setOccasionsWeigth(int $occasionsWeigth): static
    {
        $this->occasionsWeigth = $occasionsWeigth;

        return $this;
    }

    public function getOccasionsPriceWithoutTax(): ?int
    {
        return $this->occasionsPriceWithoutTax;
    }

    public function setOccasionsPriceWithoutTax(int $occasionsPriceWithoutTax): static
    {
        $this->occasionsPriceWithoutTax = $occasionsPriceWithoutTax;

        return $this;
    }

    public function getBoitesWeigth(): ?int
    {
        return $this->boitesWeigth;
    }

    public function setBoitesWeigth(int $boitesWeigth): static
    {
        $this->boitesWeigth = $boitesWeigth;

        return $this;
    }

    public function getBoitesPriceWithoutTax(): ?int
    {
        return $this->boitesPriceWithoutTax;
    }

    public function setBoitesPriceWithoutTax(int $boitesPriceWithoutTax): static
    {
        $this->boitesPriceWithoutTax = $boitesPriceWithoutTax;

        return $this;
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

    public function getDiscountonpurchase(): ?int
    {
        return $this->discountonpurchase;
    }

    public function setDiscountonpurchase(int $discountonpurchase): static
    {
        $this->discountonpurchase = $discountonpurchase;

        return $this;
    }

    public function getDiscountonpurchaseinpurcentage(): ?int
    {
        return $this->discountonpurchaseinpurcentage;
    }

    public function setDiscountonpurchaseinpurcentage(?int $discountonpurchaseinpurcentage): static
    {
        $this->discountonpurchaseinpurcentage = $discountonpurchaseinpurcentage;

        return $this;
    }

    public function getVoucherDiscountValueUsed(): ?int
    {
        return $this->voucherDiscountValueUsed;
    }

    public function setVoucherDiscountValueUsed(int $voucherDiscountValueUsed): static
    {
        $this->voucherDiscountValueUsed = $voucherDiscountValueUsed;

        return $this;
    }

    /**
     * @return Collection<int, VoucherDiscount>
     */
    public function getVoucherDiscounts(): Collection
    {
        return $this->voucherDiscounts;
    }

    public function addVoucherDiscount(VoucherDiscount $voucherDiscount): static
    {
        if (!$this->voucherDiscounts->contains($voucherDiscount)) {
            $this->voucherDiscounts->add($voucherDiscount);
            $voucherDiscount->addDocumentLineTotal($this);
        }

        return $this;
    }

    public function removeVoucherDiscount(VoucherDiscount $voucherDiscount): static
    {
        if ($this->voucherDiscounts->removeElement($voucherDiscount)) {
            $voucherDiscount->removeDocumentLineTotal($this);
        }

        return $this;
    }
}
