<?php

namespace App\Entity;

use App\Repository\ExpenseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExpenseRepository::class)]
class Expense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?ExpenseTemplate $template = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $amount = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column]
    private ?bool $isPlaceholder = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $paidBy = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: 2)]
    private ?string $splitRatio = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemplate(): ?ExpenseTemplate
    {
        return $this->template;
    }

    public function setTemplate(?ExpenseTemplate $template): static
    {
        $this->template = $template;

        return $this;
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

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function isPlaceholder(): ?bool
    {
        return $this->isPlaceholder;
    }

    public function setIsPlaceholder(bool $isPlaceholder): static
    {
        $this->isPlaceholder = $isPlaceholder;

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

    public function getSplitRatio(): ?string
    {
        return $this->splitRatio;
    }

    public function setSplitRatio(string $splitRatio): static
    {
        $this->splitRatio = $splitRatio;

        return $this;
    }
}
