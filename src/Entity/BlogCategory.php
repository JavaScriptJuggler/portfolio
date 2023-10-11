<?php

namespace App\Entity;

use App\Repository\BlogCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlogCategoryRepository::class)]
class BlogCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $user_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $category_name = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $formatter_caegory_name = null;

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

    public function getCategoryName(): ?string
    {
        return $this->category_name;
    }

    public function setCategoryName(?string $category_name): static
    {
        $this->category_name = $category_name;

        return $this;
    }

    public function getFormatterCaegoryName(): ?string
    {
        return $this->formatter_caegory_name;
    }

    public function setFormatterCaegoryName(?string $formatter_caegory_name): static
    {
        $this->formatter_caegory_name = $formatter_caegory_name;

        return $this;
    }
}
