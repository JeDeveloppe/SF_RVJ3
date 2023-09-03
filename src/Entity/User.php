<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nickname = null;

    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastvisite = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $membership = null;

    #[ORM\Column(nullable: true)]
    private ?int $rvj2id = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Country $country = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Address::class)]
    private Collection $addresses;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Boite::class)]
    private Collection $boites;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: OffSiteOccasionSale::class)]
    private Collection $offSiteOccasionSales;

    #[ORM\OneToMany(mappedBy: 'updatedBy', targetEntity: LegalInformation::class)]
    private Collection $legalInformation;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
        $this->boites = new ArrayCollection();
        $this->offSiteOccasionSales = new ArrayCollection();
        $this->legalInformation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): static
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

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

    public function getLastvisite(): ?\DateTimeImmutable
    {
        return $this->lastvisite;
    }

    public function setLastvisite(\DateTimeImmutable $lastvisite): static
    {
        $this->lastvisite = $lastvisite;

        return $this;
    }

    public function getMembership(): ?\DateTimeImmutable
    {
        return $this->membership;
    }

    public function setMembership(?\DateTimeImmutable $membership): static
    {
        $this->membership = $membership;

        return $this;
    }

    public function getRvj2id(): ?int
    {
        return $this->rvj2id;
    }

    public function setRvj2id(?int $rvj2id): static
    {
        $this->rvj2id = $rvj2id;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): static
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): static
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setUser($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): static
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getUser() === $this) {
                $address->setUser(null);
            }
        }

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
            $boite->setCreatedBy($this);
        }

        return $this;
    }

    public function removeBoite(Boite $boite): static
    {
        if ($this->boites->removeElement($boite)) {
            // set the owning side to null (unless already changed)
            if ($boite->getCreatedBy() === $this) {
                $boite->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OffSiteOccasionSale>
     */
    public function getOffSiteOccasionSales(): Collection
    {
        return $this->offSiteOccasionSales;
    }

    public function addOffSiteOccasionSale(OffSiteOccasionSale $offSiteOccasionSale): static
    {
        if (!$this->offSiteOccasionSales->contains($offSiteOccasionSale)) {
            $this->offSiteOccasionSales->add($offSiteOccasionSale);
            $offSiteOccasionSale->setCreatedBy($this);
        }

        return $this;
    }

    public function removeOffSiteOccasionSale(OffSiteOccasionSale $offSiteOccasionSale): static
    {
        if ($this->offSiteOccasionSales->removeElement($offSiteOccasionSale)) {
            // set the owning side to null (unless already changed)
            if ($offSiteOccasionSale->getCreatedBy() === $this) {
                $offSiteOccasionSale->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nickname ?? '#'.$this->id;
    }

    /**
     * @return Collection<int, LegalInformation>
     */
    public function getLegalInformation(): Collection
    {
        return $this->legalInformation;
    }

    public function addLegalInformation(LegalInformation $legalInformation): static
    {
        if (!$this->legalInformation->contains($legalInformation)) {
            $this->legalInformation->add($legalInformation);
            $legalInformation->setUpdatedBy($this);
        }

        return $this;
    }

    public function removeLegalInformation(LegalInformation $legalInformation): static
    {
        if ($this->legalInformation->removeElement($legalInformation)) {
            // set the owning side to null (unless already changed)
            if ($legalInformation->getUpdatedBy() === $this) {
                $legalInformation->setUpdatedBy(null);
            }
        }

        return $this;
    }
}
