<?php

namespace App\Repository;

use App\Entity\ExpenseTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExpenseTemplate>
 */
class ExpenseTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpenseTemplate::class);
    }

    public function findAllActive(): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.deletedAt IS NULL')
            ->orderBy('t.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
