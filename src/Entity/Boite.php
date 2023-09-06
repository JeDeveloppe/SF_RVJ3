<?php

namespace App\Entity;

use App\Repository\BoiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BoiteRepository::class)]
class Boite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $initeditor = null;

    #[ORM\ManyToOne(inversedBy: 'boites')]
    private ?Editor $editor = null;

    #[ORM\Column(nullable: true)]
    private ?int $year = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?bool $isDeliverable = null;

    #[ORM\Column]
    private ?bool $isOccasion = null;

    #[ORM\Column]
    private ?int $weigth = null;

    #[ORM\Column]
    private ?int $age = null;

    #[ORM\Column]
    private ?int $players = null;

    #[ORM\Column(nullable: true)]
    private ?int $htPrice = null;

    #[ORM\ManyToOne(inversedBy: 'boites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\Column]
    private ?bool $isDeee = null;

    #[ORM\Column]
    private ?bool $isOnline = null;

    #[ORM\Column(nullable:true)]
    private ?int $rvj2id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?bool $isDirectSale = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contentMessage = null;

    #[ORM\OneToMany(mappedBy: 'boite', targetEntity: Occasion::class)]
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

    public function getIniteditor(): ?string
    {
        return $this->initeditor;
    }

    public function setIniteditor(?string $initeditor): static
    {
        $this->initeditor = $initeditor;

        return $this;
    }

    public function getEditor(): ?Editor
    {
        return $this->editor;
    }

    public function setEditor(?Editor $editor): static
    {
        $this->editor = $editor;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function isIsDeliverable(): ?bool
    {
        return $this->isDeliverable;
    }

    public function setIsDeliverable(bool $isDeliverable): static
    {
        $this->isDeliverable = $isDeliverable;

        return $this;
    }

    public function isIsOccasion(): ?bool
    {
        return $this->isOccasion;
    }

    public function setIsOccasion(bool $isOccasion): static
    {
        $this->isOccasion = $isOccasion;

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

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getPlayers(): ?int
    {
        return $this->players;
    }

    public function setPlayers(int $players): static
    {
        $this->players = $players;

        return $this;
    }

    public function getHtPrice(): ?int
    {
        return $this->htPrice;
    }

    public function setHtPrice(?int $htPrice): static
    {
        $this->htPrice = $htPrice;

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

    public function isIsDeee(): ?bool
    {
        return $this->isDeee;
    }

    public function setIsDeee(bool $isDeee): static
    {
        $this->isDeee = $isDeee;

        return $this;
    }

    public function isIsOnline(): ?bool
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isIsDirectSale(): ?bool
    {
        return $this->isDirectSale;
    }

    public function setIsDirectSale(bool $isDirectSale): static
    {
        $this->isDirectSale = $isDirectSale;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getContentMessage(): ?string
    {
        return $this->contentMessage;
    }

    public function setContentMessage(?string $contentMessage): static
    {
        $this->contentMessage = $contentMessage;

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
            $occasion->setBoite($this);
        }

        return $this;
    }

    public function removeOccasion(Occasion $occasion): static
    {
        if ($this->occasions->removeElement($occasion)) {
            // set the owning side to null (unless already changed)
            if ($occasion->getBoite() === $this) {
                $occasion->setBoite(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name.' - '.$this->editor.' - '.$this->year;
    }
}
