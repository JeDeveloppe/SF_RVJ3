<?php

namespace App\Entity;

use App\Repository\SiteSettingRepository;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Marquee = null;

    #[ORM\Column(nullable: true)]
    private ?bool $maintenanceMode = null;

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

    public function getMarquee(): ?string
    {
        return $this->Marquee;
    }

    public function setMarquee(?string $Marquee): static
    {
        $this->Marquee = $Marquee;

        return $this;
    }

    public function isMaintenanceMode(): ?bool
    {
        return $this->maintenanceMode;
    }

    public function setMaintenanceMode(?bool $maintenanceMode): static
    {
        $this->maintenanceMode = $maintenanceMode;

        return $this;
    }
}
