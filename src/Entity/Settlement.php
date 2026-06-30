<?php

namespace App\Entity;

use App\Repository\SettlementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettlementRepository::class)]
class Settlement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 7)]
    private ?string $monthYear = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $paidBy = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $receivedBy = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $amount = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $timestamp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMonthYear(): ?string
    {
        return $this->monthYear;
    }

    public function setMonthYear(string $monthYear): static
    {
        $this->monthYear = $monthYear;

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

    public function getReceivedBy(): ?User
    {
        return $this->receivedBy;
    }

    public function setReceivedBy(?User $receivedBy): static
    {
        $this->receivedBy = $receivedBy;

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

    public function getTimestamp(): ?\DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeImmutable $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
