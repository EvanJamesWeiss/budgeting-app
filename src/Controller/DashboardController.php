<?php

namespace App\Controller;

use App\Service\ExpenseGenerationService;
use App\Service\LedgerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(ExpenseGenerationService $generationService, LedgerService $ledgerService): Response
    {
        $currentMonth = (new \DateTimeImmutable())->format('Y-m');
        $generationService->generateMonthlyPlaceholders($currentMonth);
        $summary = $ledgerService->calculateMonthSummary($currentMonth);

        return $this->render('dashboard/index.html.twig', [
            'summary' => $summary,
            'currentMonth' => $currentMonth,
        ]);
    }
}
