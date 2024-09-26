<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 10)]
    private ?string $isocode = null;

    #[ORM\OneToMany(mappedBy: 'country', targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'country', targetEntity: Department::class)]
    private Collection $departments;

    #[ORM\OneToMany(mappedBy: 'country', targetEntity: City::class)]
    private Collection $cities;

    #[ORM\OneToMany(mappedBy: 'country', targetEntity: LegalInformation::class)]
    private Collection $legalInformation;

    #[ORM\Column]
    private ?bool $actifInInscriptionForm = null;

    #[ORM\OneToMany(mappedBy: 'country', targetEntity: Granderegion::class)]
    private Collection $granderegions;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->departments = new ArrayCollection();
        $this->cities = new ArrayCollection();
        $this->legalInformation = new ArrayCollection();
        $this->granderegions = new ArrayCollection();
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

    public function getIsocode(): ?string
    {
        return $this->isocode;
    }

    public function setIsocode(string $isocode): static
    {
        $this->isocode = $isocode;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setCountry($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCountry() === $this) {
                $user->setCountry(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Department>
     */
    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function addDepartment(Department $department): static
    {
        if (!$this->departments->contains($department)) {
            $this->departments->add($department);
            $department->setCountry($this);
        }

        return $this;
    }

    public function removeDepartment(Department $department): static
    {
        if ($this->departments->removeElement($department)) {
            // set the owning side to null (unless already changed)
            if ($department->getCountry() === $this) {
                $department->setCountry(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, City>
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    public function addCity(City $city): static
    {
        if (!$this->cities->contains($city)) {
            $this->cities->add($city);
            $city->setCountry($this);
        }

        return $this;
    }

    public function removeCity(City $city): static
    {
        if ($this->cities->removeElement($city)) {
            // set the owning side to null (unless already changed)
            if ($city->getCountry() === $this) {
                $city->setCountry(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
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
            $legalInformation->setCountry($this);
        }

        return $this;
    }

    public function removeLegalInformation(LegalInformation $legalInformation): static
    {
        if ($this->legalInformation->removeElement($legalInformation)) {
            // set the owning side to null (unless already changed)
            if ($legalInformation->getCountry() === $this) {
                $legalInformation->setCountry(null);
            }
        }

        return $this;
    }

    public function getIsActifInInscriptionForm(): ?bool
    {
        return $this->actifInInscriptionForm;
    }

    public function setIsActifInInscriptionForm(bool $actifInInscriptionForm): static
    {
        $this->actifInInscriptionForm = $actifInInscriptionForm;

        return $this;
    }

    /**
     * @return Collection<int, Granderegion>
     */
    public function getGranderegions(): Collection
    {
        return $this->granderegions;
    }

    public function addGranderegion(Granderegion $granderegion): static
    {
        if (!$this->granderegions->contains($granderegion)) {
            $this->granderegions->add($granderegion);
            $granderegion->setCountry($this);
        }

        return $this;
    }

    public function removeGranderegion(Granderegion $granderegion): static
    {
        if ($this->granderegions->removeElement($granderegion)) {
            // set the owning side to null (unless already changed)
            if ($granderegion->getCountry() === $this) {
                $granderegion->setCountry(null);
            }
        }

        return $this;
    }
}
