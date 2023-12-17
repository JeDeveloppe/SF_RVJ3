<?php

namespace App\Entity;

use App\Repository\SiteSettingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteSettingRepository::class)]
class SiteSetting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $BlockEmailSending = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBlockEmailSending(): ?bool
    {
        return $this->BlockEmailSending;
    }

    public function setBlockEmailSending(?bool $BlockEmailSending): static
    {
        $this->BlockEmailSending = $BlockEmailSending;

        return $this;
    }
}
