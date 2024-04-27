<?php

namespace App\Entity;

use App\Repository\NumbersOfPlayersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NumbersOfPlayersRepository::class)]
class NumbersOfPlayers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'playersMin', targetEntity: Boite::class)]
    private Collection $boitesMin;

    #[ORM\Column(length: 255, nullable:true)]
    private ?string $keyword = null;

    #[ORM\OneToMany(mappedBy: 'playersMax', targetEntity: Boite::class)]
    private Collection $boitesMax;

    #[ORM\Column]
    private ?bool $isInOccasionFormSearch = null;

    #[ORM\Column(nullable: true)]
    private ?int $orderOfAppearance = null;

    public function __construct()
    {
        $this->boitesMin = new ArrayCollection();
        $this->boitesMax = new ArrayCollection();
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
     * @return Collection<int, Boite>
     */
    public function getBoitesMin(): Collection
    {
        return $this->boitesMin;
    }

    public function addBoitesMin(Boite $boitesMin): static
    {
        if (!$this->boitesMin->contains($boitesMin)) {
            $this->boitesMin->add($boitesMin);
            $boitesMin->setPlayersMin($this);
        }

        return $this;
    }

    public function removeBoitesMin(Boite $boitesMin): static
    {
        if ($this->boitesMin->removeElement($boitesMin)) {
            // set the owning side to null (unless already changed)
            if ($boitesMin->getPlayersMin() === $this) {
                $boitesMin->setPlayersMin(null);
            }
        }

        return $this;
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function setKeyword(?string $keyword): static
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Collection<int, Boite>
     */
    public function getBoitesMax(): Collection
    {
        return $this->boitesMax;
    }

    public function addBoitesMax(Boite $boitesMax): static
    {
        if (!$this->boitesMax->contains($boitesMax)) {
            $this->boitesMax->add($boitesMax);
            $boitesMax->setPlayersMax($this);
        }

        return $this;
    }

    public function removeBoitesMax(Boite $boitesMax): static
    {
        if ($this->boitesMax->removeElement($boitesMax)) {
            // set the owning side to null (unless already changed)
            if ($boitesMax->getPlayersMax() === $this) {
                $boitesMax->setPlayersMax(null);
            }
        }

        return $this;
    }

    public function getIsInOccasionFormSearch(): ?bool
    {
        return $this->isInOccasionFormSearch;
    }

    public function setIsInOccasionFormSearch(bool $isInOccasionFormSearch): static
    {
        $this->isInOccasionFormSearch = $isInOccasionFormSearch;

        return $this;
    }

    public function getOrderOfAppearance(): ?int
    {
        return $this->orderOfAppearance;
    }

    public function setOrderOfAppearance(?int $orderOfAppearance): static
    {
        $this->orderOfAppearance = $orderOfAppearance;

        return $this;
    }
}
