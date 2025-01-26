<?php

namespace App\Entity;

use App\Repository\AmbassadorRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AmbassadorRepository::class)]
class Ambassador
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $organization = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $street = null;

    #[ORM\Column(length: 18, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fullurl = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $privatefirstname = null;

    #[ORM\Column(length: 255)]
    private ?string $privatelastname = null;

    #[ORM\Column(length: 255)]
    private ?string $privatestreet = null;

    #[ORM\ManyToOne(inversedBy: 'ambassadorsprivate')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $privatecity = null;

    #[ORM\Column(length: 18)]
    private ?string $privatephone = null;

    #[ORM\Column(length: 255)]
    private ?string $privateemail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facebookLink = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $instagramLink = null;

    #[ORM\ManyToOne(inversedBy: 'ambassadors')]
    private ?City $city = null;

    #[ORM\Column]
    private ?bool $onTheCarte = null;

    #[ORM\Column]
    private ?int $colisSend = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 7, nullable: true)]
    private ?string $longitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 7, nullable: true)]
    private ?string $latitude = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrganization(): ?string
    {
        return $this->organization;
    }

    public function setOrganization(?string $organization): static
    {
        $this->organization = $organization;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFullurl(): ?string
    {
        return $this->fullurl;
    }

    public function setFullurl(?string $fullurl): static
    {
        $this->fullurl = $fullurl;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrivatefirstname(): ?string
    {
        return $this->privatefirstname;
    }

    public function setPrivatefirstname(string $privatefirstname): static
    {
        $this->privatefirstname = $privatefirstname;

        return $this;
    }

    public function getPrivatelastname(): ?string
    {
        return $this->privatelastname;
    }

    public function setPrivatelastname(string $privatelastname): static
    {
        $this->privatelastname = $privatelastname;

        return $this;
    }

    public function getPrivatestreet(): ?string
    {
        return $this->privatestreet;
    }

    public function setPrivatestreet(string $privatestreet): static
    {
        $this->privatestreet = $privatestreet;

        return $this;
    }

    public function getPrivatecity(): ?City
    {
        return $this->privatecity;
    }

    public function setPrivatecity(?City $privatecity): static
    {
        $this->privatecity = $privatecity;

        return $this;
    }

    public function getPrivatephone(): ?string
    {
        return $this->privatephone;
    }

    public function setPrivatephone(string $privatephone): static
    {
        $this->privatephone = $privatephone;

        return $this;
    }

    public function getPrivateemail(): ?string
    {
        return $this->privateemail;
    }

    public function setPrivateemail(string $privateemail): static
    {
        $this->privateemail = $privateemail;

        return $this;
    }

    public function getFacebookLink(): ?string
    {
        return $this->facebookLink;
    }

    public function setFacebookLink(?string $facebookLink): static
    {
        $this->facebookLink = $facebookLink;

        return $this;
    }

    public function getInstagramLink(): ?string
    {
        return $this->instagramLink;
    }

    public function setInstagramLink(?string $instagramLink): static
    {
        $this->instagramLink = $instagramLink;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function isOnTheCarte(): ?bool
    {
        return $this->onTheCarte;
    }

    public function setOnTheCarte(bool $onTheCarte): static
    {
        $this->onTheCarte = $onTheCarte;

        return $this;
    }

    public function getColisSend(): ?int
    {
        return $this->colisSend;
    }

    public function setColisSend(int $colisSend): static
    {
        $this->colisSend = $colisSend;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }
}
