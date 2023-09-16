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

    #[ORM\OneToMany(mappedBy: 'players', targetEntity: Boite::class)]
    private Collection $boites;

    #[ORM\Column(length: 255, nullable:true)]
    private ?string $keyword = null;

    public function __construct()
    {
        $this->boites = new ArrayCollection();
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
    public function getBoites(): Collection
    {
        return $this->boites;
    }

    public function addBoite(Boite $boite): static
    {
        if (!$this->boites->contains($boite)) {
            $this->boites->add($boite);
            $boite->setPlayers($this);
        }

        return $this;
    }

    public function removeBoite(Boite $boite): static
    {
        if ($this->boites->removeElement($boite)) {
            // set the owning side to null (unless already changed)
            if ($boite->getPlayers() === $this) {
                $boite->setPlayers(null);
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
}
