<?php

namespace App\Controller;

use App\Repository\ExpenseRepository;
use App\Service\ExpenseGenerationService;
use App\Service\LedgerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LedgerController extends AbstractController
{
    #[Route('/ledger/{monthYear}', name: 'app_ledger', defaults: ['monthYear' => null])]
    public function index(
        ?string $monthYear,
        ExpenseGenerationService $generationService,
        LedgerService $ledgerService,
        ExpenseRepository $expenseRepository
    ): Response {
        if (!$monthYear) {
            $monthYear = (new \DateTimeImmutable())->format('Y-m');
        }

        $generationService->generateMonthlyPlaceholders($monthYear);
        $summary = $ledgerService->calculateMonthSummary($monthYear);

        $startDate = new \DateTimeImmutable($monthYear . '-01 00:00:00');
        $endDate = $startDate->modify('last day of this month 23:59:59');

        $expenses = $expenseRepository->createQueryBuilder('e')
            ->where('e.date >= :start')
            ->andWhere('e.date <= :end')
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('e.date', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('ledger/index.html.twig', [
            'expenses' => $expenses,
            'summary' => $summary,
            'monthYear' => $monthYear,
        ]);
    }
}
