<?php

namespace App\Entity;

use App\Repository\DocumentStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentStatusRepository::class)]
class DocumentStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'documentStatus', targetEntity: Document::class)]
    private Collection $documents;

    #[ORM\Column]
    private ?bool $isToBeTraitedDaily = null;

    #[ORM\Column(length: 255)]
    private ?string $action = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adminActionText = null;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
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
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setDocumentStatus($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): static
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getDocumentStatus() === $this) {
                $document->setDocumentStatus(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function isIsToBeTraitedDaily(): ?bool
    {
        return $this->isToBeTraitedDaily;
    }

    public function setIsToBeTraitedDaily(bool $isToBeTraitedDaily): static
    {
        $this->isToBeTraitedDaily = $isToBeTraitedDaily;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getAdminActionText(): ?string
    {
        return $this->adminActionText;
    }

    public function setAdminActionText(?string $adminActionText): static
    {
        $this->adminActionText = $adminActionText;

        return $this;
    }
}
