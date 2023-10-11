<?php

namespace App\Entity;

use App\Repository\ExperienceOverViewRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExperienceOverViewRepository::class)]
class ExperienceOverView
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $user_id = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $experience_heading = null;

    #[ORM\Column(length: 3000, nullable: true)]
    private ?string $experience_description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $total_projects = null;

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

    public function getExperienceHeading(): ?string
    {
        return $this->experience_heading;
    }

    public function setExperienceHeading(?string $experience_heading): static
    {
        $this->experience_heading = $experience_heading;

        return $this;
    }

    public function getExperienceDescription(): ?string
    {
        return $this->experience_description;
    }

    public function setExperienceDescription(?string $experience_description): static
    {
        $this->experience_description = $experience_description;

        return $this;
    }

    public function getTotalProjects(): ?string
    {
        return $this->total_projects;
    }

    public function setTotalProjects(?string $total_projects): static
    {
        $this->total_projects = $total_projects;

        return $this;
    }
}
