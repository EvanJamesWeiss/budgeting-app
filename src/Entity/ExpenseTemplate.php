<?php

namespace App\Entity;

use App\Repository\ExpenseTemplateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExpenseTemplateRepository::class)]
class ExpenseTemplate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $defaultAmount = null;

    #[ORM\Column]
    private ?bool $isStatic = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $paidBy = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: 2)]
    private ?string $defaultSplitRatio = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDefaultAmount(): ?string
    {
        return $this->defaultAmount;
    }

    public function setDefaultAmount(?string $defaultAmount): static
    {
        $this->defaultAmount = $defaultAmount;

        return $this;
    }

    public function isStatic(): ?bool
    {
        return $this->isStatic;
    }

    public function setIsStatic(bool $isStatic): static
    {
        $this->isStatic = $isStatic;

        return $this;
    }

    public function getPaidBy(): ?User
    {
        return $this->paidBy;
    }

    public function setPaidBy(?User $paidBy): static
    {
        $this->paidBy = $paidBy;

        return $this;
    }

    public function getDefaultSplitRatio(): ?string
    {
        return $this->defaultSplitRatio;
    }

    public function setDefaultSplitRatio(string $defaultSplitRatio): static
    {
        $this->defaultSplitRatio = $defaultSplitRatio;

        return $this;
    }
}
