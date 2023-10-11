<?php

namespace App\Entity;

use App\Repository\OfficeExperiencesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OfficeExperiencesRepository::class)]
class OfficeExperiences
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $user_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $office_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $designation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $years = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $location = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(?int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getOfficeName(): ?string
    {
        return $this->office_name;
    }

    public function setOfficeName(?string $office_name): static
    {
        $this->office_name = $office_name;

        return $this;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(?string $designation): static
    {
        $this->designation = $designation;

        return $this;
    }

    public function getYears(): ?string
    {
        return $this->years;
    }

    public function setYears(?string $years): static
    {
        $this->years = $years;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }
}
