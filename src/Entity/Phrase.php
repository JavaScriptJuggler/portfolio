<?php

namespace App\Entity;

use App\Repository\PhraseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhraseRepository::class)]
class Phrase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phrase_name = null;

    #[ORM\Column(length: 5000, nullable: true)]
    private ?string $phrase_description = null;

    #[ORM\Column(nullable: true)]
    private ?int $user_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhraseName(): ?string
    {
        return $this->phrase_name;
    }

    public function setPhraseName(?string $phrase_name): static
    {
        $this->phrase_name = $phrase_name;

        return $this;
    }

    public function getPhraseDescription(): ?string
    {
        return $this->phrase_description;
    }

    public function setPhraseDescription(?string $phrase_description): static
    {
        $this->phrase_description = $phrase_description;

        return $this;
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
}
