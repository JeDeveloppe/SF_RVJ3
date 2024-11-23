<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ConditionOccasionRepository;

#[ORM\Entity(repositoryClass: ConditionOccasionRepository::class)]
class ConditionOccasion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'boxCondition', targetEntity: Occasion::class)]
    private Collection $boxConditions;

    #[ORM\OneToMany(mappedBy: 'equipmentCondition', targetEntity: Occasion::class)]
    private Collection $equipmentConditions;

    #[ORM\OneToMany(mappedBy: 'gameRule', targetEntity: Occasion::class)]
    private Collection $gameRules;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $color = null;

    #[ORM\Column]
    private ?int $discount = null;

    public function __construct()
    {
        $this->boxConditions = new ArrayCollection();
        $this->equipmentConditions = new ArrayCollection();
        $this->gameRules = new ArrayCollection();
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
     * @return Collection<int, Occasion>
     */
    public function getBoxConditions(): Collection
    {
        return $this->boxConditions;
    }

    public function addBoxCondition(Occasion $boxCondition): static
    {
        if (!$this->boxConditions->contains($boxCondition)) {
            $this->boxConditions->add($boxCondition);
            $boxCondition->setBoxCondition($this);
        }

        return $this;
    }

    public function removeBoxCondition(Occasion $boxCondition): static
    {
        if ($this->boxConditions->removeElement($boxCondition)) {
            // set the owning side to null (unless already changed)
            if ($boxCondition->getBoxCondition() === $this) {
                $boxCondition->setBoxCondition(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Occasion>
     */
    public function getEquipmentConditions(): Collection
    {
        return $this->equipmentConditions;
    }

    public function addEquipmentCondition(Occasion $equipmentCondition): static
    {
        if (!$this->equipmentConditions->contains($equipmentCondition)) {
            $this->equipmentConditions->add($equipmentCondition);
            $equipmentCondition->setEquipmentCondition($this);
        }

        return $this;
    }

    public function removeEquipmentCondition(Occasion $equipmentCondition): static
    {
        if ($this->equipmentConditions->removeElement($equipmentCondition)) {
            // set the owning side to null (unless already changed)
            if ($equipmentCondition->getEquipmentCondition() === $this) {
                $equipmentCondition->setEquipmentCondition(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Occasion>
     */
    public function getGameRules(): Collection
    {
        return $this->gameRules;
    }

    public function addGameRule(Occasion $gameRule): static
    {
        if (!$this->gameRules->contains($gameRule)) {
            $this->gameRules->add($gameRule);
            $gameRule->setGameRule($this);
        }

        return $this;
    }

    public function removeGameRule(Occasion $gameRule): static
    {
        if ($this->gameRules->removeElement($gameRule)) {
            // set the owning side to null (unless already changed)
            if ($gameRule->getGameRule() === $this) {
                $gameRule->setGameRule(null);
            }
        }

        return $this;
    }
    
    public function __toString()
    {
        return $this->name;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(int $discount): static
    {
        $this->discount = $discount;

        return $this;
    }
}
