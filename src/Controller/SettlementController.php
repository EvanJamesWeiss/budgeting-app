<?php

namespace App\Controller;

use App\Entity\Settlement;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettlementController extends AbstractController
{
    #[Route('/settlement/log', name: 'app_settlement_log', methods: ['POST'])]
    public function log(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $monthYear = $request->request->get('monthYear');
        $paidById = (int) $request->request->get('paid_by_id');
        $amount = $request->request->get('amount');

        $paidBy = $userRepository->find($paidById);
        $receivedById = ($paidById === 1) ? 2 : 1;
        $receivedBy = $userRepository->find($receivedById);

        if (!$paidBy || !$receivedBy) {
            throw $this->createNotFoundException('User not found');
        }

        $settlement = new Settlement();
        $settlement->setMonthYear($monthYear);
        $settlement->setPaidBy($paidBy);
        $settlement->setReceivedBy($receivedBy);
        $settlement->setAmount($amount);
        $settlement->setTimestamp(new \DateTimeImmutable());

        $entityManager->persist($settlement);
        $entityManager->flush();

        // Redirect back to ledger or where it came from
        $referer = $request->headers->get('referer');
        return $referer ? $this->redirect($referer) : $this->redirectToRoute('app_dashboard');
    }
}
