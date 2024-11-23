<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'stock', targetEntity: Occasion::class)]
    private Collection $occasions;

    public function __construct()
    {
        $this->occasions = new ArrayCollection();
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
    public function getOccasions(): Collection
    {
        return $this->occasions;
    }

    public function addOccasion(Occasion $occasion): static
    {
        if (!$this->occasions->contains($occasion)) {
            $this->occasions->add($occasion);
            $occasion->setStock($this);
        }

        return $this;
    }

    public function removeOccasion(Occasion $occasion): static
    {
        if ($this->occasions->removeElement($occasion)) {
            // set the owning side to null (unless already changed)
            if ($occasion->getStock() === $this) {
                $occasion->setStock(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
