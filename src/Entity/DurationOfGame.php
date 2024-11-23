<?php

namespace App\Entity;

use App\Repository\DurationOfGameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DurationOfGameRepository::class)]
class DurationOfGame
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'durationGame', targetEntity: Boite::class)]
    private Collection $boites;

    #[ORM\Column]
    private ?int $orderOfAppearance = null;

    #[ORM\Column]
    private ?bool $displayInForm = null;

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
            $boite->setDurationGame($this);
        }

        return $this;
    }

    public function removeBoite(Boite $boite): static
    {
        if ($this->boites->removeElement($boite)) {
            // set the owning side to null (unless already changed)
            if ($boite->getDurationGame() === $this) {
                $boite->setDurationGame(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getOrderOfAppearance(): ?int
    {
        return $this->orderOfAppearance;
    }

    public function setOrderOfAppearance(int $orderOfAppearance): static
    {
        $this->orderOfAppearance = $orderOfAppearance;

        return $this;
    }

    public function isDisplayInForm(): ?bool
    {
        return $this->displayInForm;
    }

    public function setDisplayInForm(bool $displayInForm): static
    {
        $this->displayInForm = $displayInForm;

        return $this;
    }
}
