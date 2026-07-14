<?php

namespace App\Service;

use App\Entity\ExpenseTemplate;
use Doctrine\ORM\EntityManagerInterface;

class RecurringPaymentService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function create(ExpenseTemplate $template): void
    {
        $this->em->persist($template);
        $this->em->flush();
    }

    public function update(ExpenseTemplate $template): void
    {
        $this->em->flush();
    }

    public function softDelete(ExpenseTemplate $template): void
    {
        $template->softDelete();
        $this->em->flush();
    }
}
