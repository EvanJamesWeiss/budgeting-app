<?php

namespace App\Controller;

use App\Service\LedgerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AllTimeController extends AbstractController
{
    #[Route('/all-time', name: 'app_all_time')]
    public function index(LedgerService $ledgerService): Response
    {
        $summaries = $ledgerService->calculateAllTimeBalances();

        return $this->render('all_time/index.html.twig', [
            'summaries' => $summaries,
        ]);
    }
}
