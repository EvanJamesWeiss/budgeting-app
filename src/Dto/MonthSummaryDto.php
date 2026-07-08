<?php

namespace App\Dto;

readonly class MonthSummaryDto
{
    public function __construct(
        public string $monthYear,
        public float $totalSpent,
        public float $user1Spent,
        public float $user2Spent,
        public float $netOwedAmount,
        public ?int $debtorId,
        public ?int $creditorId,
        public ?string $debtorName,
        public ?string $creditorName,
        public bool $isSettled,
        public string $user1Name,
        public string $user2Name
    ) {}
}
