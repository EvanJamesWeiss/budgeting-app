<?php

namespace App\Service;

use App\Entity\Expense;
use App\Repository\ExpenseRepository;
use App\Repository\ExpenseTemplateRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

readonly class ExpenseGenerationService
{
    public function __construct(
        private ExpenseTemplateRepository $templateRepository,
        private ExpenseRepository         $expenseRepository,
        private EntityManagerInterface    $entityManager
    ) {}

    /**
     * @throws Exception
     */
    public function generateMonthlyPlaceholders(string $monthYear): void
    {
        $startDate = new DateTimeImmutable($monthYear . '-01 00:00:00');
        $endDate = $startDate->modify('last day of this month 23:59:59');

        $templates = $this->templateRepository->findAllActive();

        foreach ($templates as $template) {
            // Check if expense already exists for this template in this month
            $existing = $this->expenseRepository->createQueryBuilder('e')
                ->where('e.template = :template')
                ->andWhere('e.date >= :start')
                ->andWhere('e.date <= :end')
                ->setParameter('template', $template)
                ->setParameter('start', $startDate)
                ->setParameter('end', $endDate)
                ->getQuery()
                ->getOneOrNullResult();

            if (!$existing) {
                $expense = new Expense();
                $expense->setTemplate($template);
                $expense->setTitle($template->getTitle());
                $expense->setDate($startDate); // Default to 1st of the month
                $expense->setPaidBy($template->getPaidBy());
                $expense->setSplitRatio($template->getDefaultSplitRatio());

                if ($template->isStatic()) {
                    $expense->setAmount($template->getDefaultAmount() ?? '0.00');
                    $expense->setIsPlaceholder(false);
                } else {
                    $expense->setAmount('0.00');
                    $expense->setIsPlaceholder(true);
                }

                $this->entityManager->persist($expense);
            }
        }

        $this->entityManager->flush();
    }
}
