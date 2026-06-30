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
        public bool $isSettled
    ) {}
}
