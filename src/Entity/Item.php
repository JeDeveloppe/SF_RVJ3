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

    #[ORM\ManyToMany(targetEntity: Boite::class, inversedBy: 'items')]
    private Collection $boite;

    #[ORM\OneToMany(mappedBy: 'Item', targetEntity: Panier::class)]
    private Collection $paniers;

    #[ORM\Column]
    private ?int $weigth = null;

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: DocumentLine::class)]
    private Collection $documentLines;

    public function __construct()
    {
        $this->itemGroup = new ArrayCollection();
        $this->boite = new ArrayCollection();
        $this->paniers = new ArrayCollection();
        $this->documentLines = new ArrayCollection();
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
     * @return Collection<int, Boite>
     */
    public function getBoite(): Collection
    {
        return $this->boite;
    }

    public function addBoite(Boite $boite): static
    {
        if (!$this->boite->contains($boite)) {
            $this->boite->add($boite);
        }

        return $this;
    }

    public function removeBoite(Boite $boite): static
    {
        $this->boite->removeElement($boite);

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
}
