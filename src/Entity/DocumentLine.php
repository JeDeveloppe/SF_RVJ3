<?php

namespace App\Entity;

use App\Repository\DocumentLineRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentLineRepository::class)]
class DocumentLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'documentLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Document $document = null;

    #[ORM\ManyToOne(inversedBy: 'documentLines')]
    private ?Boite $boite = null;

    #[ORM\ManyToOne(inversedBy: 'documentLines')]
    private ?Occasion $occasion = null;

    #[ORM\Column]
    private ?int $priceExcludingTax = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $question = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $answer = null;

    #[ORM\Column(nullable: true)]
    private ?int $rvj2idboite = null;

    #[ORM\Column(nullable: true)]
    private ?int $rvj2idoccasion = null;

    #[ORM\ManyToOne(inversedBy: 'documentLines')]
    private ?Item $item = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): static
    {
        $this->document = $document;

        return $this;
    }

    public function getBoite(): ?Boite
    {
        return $this->boite;
    }

    public function setBoite(?Boite $boite): static
    {
        $this->boite = $boite;

        return $this;
    }

    public function getOccasion(): ?Occasion
    {
        return $this->occasion;
    }

    public function setOccasion(?Occasion $occasion): static
    {
        $this->occasion = $occasion;

        //on met a jour l'occasion en hors ligne et comme quoi il est vendu
        $occasion->setIsBilled(true)->setIsOnline(false);

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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(?string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(?string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function getRvj2idboite(): ?int
    {
        return $this->rvj2idboite;
    }

    public function setRvj2idboite(?int $rvj2idboite): static
    {
        $this->rvj2idboite = $rvj2idboite;

        return $this;
    }

    public function getRvj2idoccasion(): ?int
    {
        return $this->rvj2idoccasion;
    }

    public function setRvj2idoccasion(?int $rvj2idoccasion): static
    {
        $this->rvj2idoccasion = $rvj2idoccasion;

        return $this;
    }

    public function __toString()
    {
        return $this->id;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): static
    {
        $this->item = $item;

        return $this;
    }
}
