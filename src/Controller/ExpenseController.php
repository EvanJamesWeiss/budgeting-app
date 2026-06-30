<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExpenseController extends AbstractController
{
    #[Route('/expense/add', name: 'app_expense_add', methods: ['POST'])]
    public function add(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $title = $request->request->get('title');
        $amount = $request->request->get('amount');
        $paidById = $request->request->get('paid_by_id');
        $dateStr = $request->request->get('date');
        $splitRatio = $request->request->get('split_ratio');

        $user = $userRepository->find($paidById);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $expense = new Expense();
        $expense->setTitle($title);
        $expense->setAmount($amount);
        $expense->setPaidBy($user);
        $expense->setDate(new \DateTimeImmutable($dateStr));
        $expense->setIsPlaceholder(false);
        $expense->setSplitRatio($splitRatio ?: '0.50');

        $entityManager->persist($expense);
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/expense/update/{id}', name: 'app_expense_update', methods: ['POST'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $expense = $entityManager->getRepository(Expense::class)->find($id);
        if (!$expense) {
            throw $this->createNotFoundException('Expense not found');
        }

        $amount = $request->request->get('amount');
        $expense->setAmount($amount);
        $expense->setIsPlaceholder(false);

        $entityManager->flush();

        return $this->redirectToRoute('app_ledger', ['monthYear' => $expense->getDate()->format('Y-m')]);
    }
}
