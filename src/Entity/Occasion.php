<?php

namespace App\Entity;

use App\Repository\OccasionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OccasionRepository::class)]

class Occasion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'occasions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Boite $boite = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $information = null;

    #[ORM\Column]
    private ?bool $isNew = null;

    #[ORM\ManyToOne(inversedBy: 'boxConditions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ConditionOccasion $boxCondition = null;

    #[ORM\ManyToOne(inversedBy: 'equipmentConditions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ConditionOccasion $equipmentCondition = null;

    #[ORM\ManyToOne(inversedBy: 'gameRules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ConditionOccasion $gameRule = null;

    #[ORM\Column]
    private ?bool $isOnline = null;

    #[ORM\Column(nullable: true)]
    private ?int $rvj2id = null;

    #[ORM\ManyToOne(inversedBy: 'occasions')]
    private ?OffSiteOccasionSale $offSiteSale = null;

    #[ORM\ManyToOne(inversedBy: 'occasions')]
    private ?OffSiteOccasionSale $offSiteOccasionSale = null;

    #[ORM\ManyToOne(inversedBy: 'occasions')]
    private ?User $createdBy = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'occasion', targetEntity: DocumentLine::class)]
    private Collection $documentLines;

    #[ORM\OneToMany(mappedBy: 'occasion', targetEntity: Panier::class)]
    private Collection $paniers;

    #[ORM\Column]
    private ?int $priceWithoutTax = null;

    #[ORM\ManyToOne(inversedBy: 'occasions')]
    private ?Reserve $reserve = null;

    #[ORM\ManyToOne(inversedBy: 'occasions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stock $stock = null;

    #[ORM\Column(nullable: true)]
    private ?int $discountedPriceWithoutTax = null;

    private ?int $virtualPriceWithoutTax = null;

    public function __construct()
    {
        $this->documentLines = new ArrayCollection();
        $this->paniers = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBoite(): ?Boite
    {
        return $this->boite;
    }

    public function setBoite(?Boite $boite): static
    {
        $this->boite = $boite;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(?string $information): static
    {
        $this->information = $information;

        return $this;
    }

    public function isIsNew(): ?bool
    {
        return $this->isNew;
    }

    public function setIsNew(bool $isNew): static
    {
        $this->isNew = $isNew;

        return $this;
    }

    public function getBoxCondition(): ?ConditionOccasion
    {
        return $this->boxCondition;
    }

    public function setBoxCondition(?ConditionOccasion $boxCondition): static
    {
        $this->boxCondition = $boxCondition;

        return $this;
    }

    public function getEquipmentCondition(): ?ConditionOccasion
    {
        return $this->equipmentCondition;
    }

    public function setEquipmentCondition(?ConditionOccasion $equipmentCondition): static
    {
        $this->equipmentCondition = $equipmentCondition;

        return $this;
    }

    public function getGameRule(): ?ConditionOccasion
    {
        return $this->gameRule;
    }

    public function setGameRule(?ConditionOccasion $gameRule): static
    {
        $this->gameRule = $gameRule;

        return $this;
    }

    public function getIsOnline(): ?bool
    {
        return $this->isOnline;
    }

    public function setIsOnline(bool $isOnline): static
    {
        $this->isOnline = $isOnline;

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

    public function __toString()
    {
        return $this->reference;
    }

    public function getOffSiteSale(): ?OffSiteOccasionSale
    {
        return $this->offSiteSale;
    }

    public function setOffSiteSale(?OffSiteOccasionSale $offSiteSale): static
    {
        $this->offSiteSale = $offSiteSale;

        return $this;
    }

    public function getOffSiteOccasionSale(): ?OffSiteOccasionSale
    {
        return $this->offSiteOccasionSale;
    }

    public function setOffSiteOccasionSale(?OffSiteOccasionSale $offSiteOccasionSale): static
    {
        $this->offSiteOccasionSale = $offSiteOccasionSale;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
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
            $documentLine->setOccasion($this);
        }

        return $this;
    }

    public function removeDocumentLine(DocumentLine $documentLine): static
    {
        if ($this->documentLines->removeElement($documentLine)) {
            // set the owning side to null (unless already changed)
            if ($documentLine->getOccasion() === $this) {
                $documentLine->setOccasion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Panier>
     */
    public function getPaniers(): Collection
    {
        return $this->paniers;
    }

    public function addPanier(Panier $panier): static
    {
        if (!$this->paniers->contains($panier)) {
            $this->paniers->add($panier);
            $panier->setOccasion($this);
        }

        return $this;
    }

    public function removePanier(Panier $panier): static
    {
        if ($this->paniers->removeElement($panier)) {
            // set the owning side to null (unless already changed)
            if ($panier->getOccasion() === $this) {
                $panier->setOccasion(null);
            }
        }

        return $this;
    }

    public function getPriceWithoutTax(): ?int
    {
        return $this->priceWithoutTax;
    }

    public function setPriceWithoutTax(int $priceWithoutTax): static
    {
        $this->priceWithoutTax = $priceWithoutTax;

        return $this;
    }

    public function getReserve(): ?Reserve
    {
        return $this->reserve;
    }

    public function setReserve(?Reserve $reserve): static
    {
        $this->reserve = $reserve;

        return $this;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(?Stock $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getDiscountedPriceWithoutTax(): ?int
    {
        return $this->discountedPriceWithoutTax ? $this->discountedPriceWithoutTax : 0.00;
    }

    public function setDiscountedPriceWithoutTax(?int $discountedPriceWithoutTax): static
    {
        $this->discountedPriceWithoutTax = $discountedPriceWithoutTax;

        return $this;
    }

    //get box conditon and rules
    public function getBoxEquipnmentAndRulesConditions(): ?string
    {
        return $this->getBoxCondition().' / '.$this->getEquipmentCondition().' / '.$this->getGameRule();
    }

    public function getPriceWithoutTaxAndDiscountedPriceWithoutTax(): ?string
    {
        if($this->getDiscountedPriceWithoutTax() == 0){
            
            $priceDiscount = " / NON";

        }else{
            
            $priceDiscount = " / ".number_format($this->getDiscountedPriceWithoutTax() / 100, 2, '.', ' ');

        }

        return number_format($this->getPriceWithoutTax() / 100, 2, '.', ' ').$priceDiscount;
    }

    public function getVirtualPriceWithoutTax(): ?int
    {
        return $this->virtualPriceWithoutTax;
    }

    public function setVirtualPriceWithoutTax(int $virtualPriceWithoutTax): static
    {
        $this->virtualPriceWithoutTax = $virtualPriceWithoutTax;

        return $this;
    }
}
