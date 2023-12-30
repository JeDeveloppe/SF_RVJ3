<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: ItemGroup::class, inversedBy: 'items')]
    private Collection $itemGroup;

    #[ORM\Column]
    private ?int $stockForSale = null;

    #[ORM\Column]
    private ?int $priceExcludingTax = null;

    #[ORM\OneToMany(mappedBy: 'Item', targetEntity: Panier::class)]
    private Collection $paniers;

    #[ORM\Column]
    private ?int $weigth = null;

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: DocumentLine::class)]
    private Collection $documentLines;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Envelope $Envelope = null;

    #[ORM\ManyToMany(targetEntity: Boite::class, inversedBy: 'itemsOrigine')]
    private Collection $BoiteOrigine;

    #[ORM\ManyToMany(targetEntity: Boite::class, mappedBy: 'itemsSecondaire')]
    private Collection $BoiteSecondaire;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reference = null;

    public function __construct()
    {
        $this->itemGroup = new ArrayCollection();
        $this->paniers = new ArrayCollection();
        $this->documentLines = new ArrayCollection();
        $this->BoiteOrigine = new ArrayCollection();
        $this->BoiteSecondaire = new ArrayCollection();
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

    /**
     * @return Collection<int, ItemGroup>
     */
    public function getItemGroup(): Collection
    {
        return $this->itemGroup;
    }

    public function addItemGroup(ItemGroup $itemGroup): static
    {
        if (!$this->itemGroup->contains($itemGroup)) {
            $this->itemGroup->add($itemGroup);
        }

        return $this;
    }

    public function removeItemGroup(ItemGroup $itemGroup): static
    {
        $this->itemGroup->removeElement($itemGroup);

        return $this;
    }

    public function getStockForSale(): ?int
    {
        return $this->stockForSale;
    }

    public function setStockForSale(int $stockForSale): static
    {
        $this->stockForSale = $stockForSale;

        return $this;
    }

    public function getPriceExcludingTax(): ?int
    {
        return $this->priceExcludingTax;
    }

    public function setPriceExcludingTax(int $priceExcludingTax): static
    {
        $this->priceExcludingTax = $priceExcludingTax;

        return $this;
    }

    public function __toString()
    {
        return $this->name.' (Qté en stock: '.$this->stockForSale.')';
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
            $panier->setItem($this);
        }

        return $this;
    }

    public function removePanier(Panier $panier): static
    {
        if ($this->paniers->removeElement($panier)) {
            // set the owning side to null (unless already changed)
            if ($panier->getItem() === $this) {
                $panier->setItem(null);
            }
        }

        return $this;
    }

    public function getWeigth(): ?int
    {
        return $this->weigth;
    }

    public function setWeigth(int $weigth): static
    {
        $this->weigth = $weigth;

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
            $documentLine->setItem($this);
        }

        return $this;
    }

    public function removeDocumentLine(DocumentLine $documentLine): static
    {
        if ($this->documentLines->removeElement($documentLine)) {
            // set the owning side to null (unless already changed)
            if ($documentLine->getItem() === $this) {
                $documentLine->setItem(null);
            }
        }

        return $this;
    }

    public function getEnvelope(): ?Envelope
    {
        return $this->Envelope;
    }

    public function setEnvelope(?Envelope $Envelope): static
    {
        $this->Envelope = $Envelope;

        return $this;
    }

    /**
     * @return Collection<int, Boite>
     */
    public function getBoiteOrigine(): Collection
    {
        return $this->BoiteOrigine;
    }

    public function addBoiteOrigine(Boite $boiteOrigine): static
    {
        if (!$this->BoiteOrigine->contains($boiteOrigine)) {
            $this->BoiteOrigine->add($boiteOrigine);
        }

        return $this;
    }

    public function removeBoiteOrigine(Boite $boiteOrigine): static
    {
        $this->BoiteOrigine->removeElement($boiteOrigine);

        return $this;
    }

    /**
     * @return Collection<int, Boite>
     */
    public function getBoiteSecondaire(): Collection
    {
        return $this->BoiteSecondaire;
    }

    public function addBoiteSecondaire(Boite $boiteSecondaire): static
    {
        if (!$this->BoiteSecondaire->contains($boiteSecondaire)) {
            $this->BoiteSecondaire->add($boiteSecondaire);
        }

        return $this;
    }

    public function removeBoiteSecondaire(Boite $boiteSecondaire): static
    {
        $this->BoiteSecondaire->removeElement($boiteSecondaire);

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

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }
}
