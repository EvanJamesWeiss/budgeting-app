<?php

namespace App\Service;

use App\Dto\MonthSummaryDto;
use App\Repository\ExpenseRepository;
use App\Repository\SettlementRepository;
use App\Repository\UserRepository;

class LedgerService
{
    public function __construct(
        private ExpenseRepository $expenseRepository,
        private SettlementRepository $settlementRepository,
        private UserRepository $userRepository
    ) {}

    public function calculateMonthSummary(string $monthYear): MonthSummaryDto
    {
        $user1 = $this->userRepository->find(1);
        $user2 = $this->userRepository->find(2);

        $user1Name = $user1 ? $user1->getName() : 'User 1';
        $user2Name = $user2 ? $user2->getName() : 'User 2';

        // 1. Fetch expenses
        // monthYear format is YYYY-MM
        $startDate = new \DateTimeImmutable($monthYear . '-01 00:00:00');
        $endDate = $startDate->modify('last day of this month 23:59:59');

        $expenses = $this->expenseRepository->createQueryBuilder('e')
            ->where('e.date >= :start')
            ->andWhere('e.date <= :end')
            ->andWhere('e.isPlaceholder = :isPlaceholder')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->setParameter('isPlaceholder', false)
            ->getQuery()
            ->getResult();

        // 2. Fetch settlements
        $settlements = $this->settlementRepository->findBy(['monthYear' => $monthYear]);

        $totalSpent = 0.0;
        $user1Spent = 0.0;
        $user2Spent = 0.0;
        
        $user2OwesUser1 = 0.0;
        $user1OwesUser2 = 0.0;

        foreach ($expenses as $expense) {
            $amount = (float) $expense->getAmount();
            $splitRatio = (float) $expense->getSplitRatio();
            $paidById = $expense->getPaidBy()->getId();

            $totalSpent += $amount;

            if ($paidById === 1) {
                $user1Spent += $amount;
                $user2OwesUser1 += $amount * $splitRatio;
            } else {
                $user2Spent += $amount;
                $user1OwesUser2 += $amount * $splitRatio;
            }
        }

        $user2PaidUser1 = 0.0;
        $user1PaidUser2 = 0.0;

        foreach ($settlements as $settlement) {
            $amount = (float) $settlement->getAmount();
            if ($settlement->getPaidBy()->getId() === 2 && $settlement->getReceivedBy()->getId() === 1) {
                $user2PaidUser1 += $amount;
            } elseif ($settlement->getPaidBy()->getId() === 1 && $settlement->getReceivedBy()->getId() === 2) {
                $user1PaidUser2 += $amount;
            }
        }

        // Net Balance
        // Positive means User 2 owes User 1
        $netBalance = ($user2OwesUser1 - $user1OwesUser2) - ($user2PaidUser1 - $user1PaidUser2);

        $debtorId = null;
        $creditorId = null;
        $netOwedAmount = abs($netBalance);

        $debtorName = null;
        $creditorName = null;

        if ($netBalance > 0.001) {
            $debtorId = 2;
            $creditorId = 1;
            $debtorName = $user2Name;
            $creditorName = $user1Name;
        } elseif ($netBalance < -0.001) {
            $debtorId = 1;
            $creditorId = 2;
            $debtorName = $user1Name;
            $creditorName = $user2Name;
        }

        $isSettled = $netOwedAmount < 0.001;

        return new MonthSummaryDto(
            monthYear: $monthYear,
            totalSpent: $totalSpent,
            user1Spent: $user1Spent,
            user2Spent: $user2Spent,
            netOwedAmount: $netOwedAmount,
            debtorId: $debtorId,
            creditorId: $creditorId,
            debtorName: $debtorName,
            creditorName: $creditorName,
            isSettled: $isSettled,
            user1Name: $user1Name,
            user2Name: $user2Name
        );
    }

    public function calculateAllTimeBalances(): array
    {
        // 1. Query all historical months present across both Expense and Settlement tables.
        $expenseMonths = $this->expenseRepository->createQueryBuilder('e')
            ->select("SUBSTRING(e.date, 1, 7) as monthYear")
            ->groupBy('monthYear')
            ->getQuery()
            ->getResult();

        $settlementMonths = $this->settlementRepository->createQueryBuilder('s')
            ->select('s.monthYear')
            ->groupBy('s.monthYear')
            ->getQuery()
            ->getResult();

        $allMonths = array_unique(array_merge(
            array_column($expenseMonths, 'monthYear'),
            array_column($settlementMonths, 'monthYear')
        ));
        sort($allMonths);

        $summaries = [];
        foreach ($allMonths as $monthYear) {
            if ($monthYear === null) continue;
            $summary = $this->calculateMonthSummary($monthYear);
            if (!$summary->isSettled) {
                $summaries[] = $summary;
            }
        }

        return $summaries;
    }
}
