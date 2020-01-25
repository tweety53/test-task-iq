<?php
declare(strict_types=1);

namespace App\Repository;


use App\Entity\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class AccountRepository
 * @package App\Repository
 */
class AccountRepository extends ServiceEntityRepository
{
    /**
     * AccountRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function isAccountExists(int $id): bool
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.userId')
            ->where('a.userId = :userId')
            ->setParameter('userId', $id);

        $query = $qb->getQuery();

        try {
            $exists = null !== $query->setMaxResults(1)->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            $exists = false;
        }

        return $exists;
    }

    /**
     * @param int $id
     * @param string $amount
     * @return bool
     */
    public function accountHasEnoughFunds(int $id, string $amount): bool
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a.userId')
            ->where('a.userId = :userId')
            ->andWhere('a.amount >= :amount')
            ->setParameter('userId', $id)
            ->setParameter('amount', $amount);

        $query = $qb->getQuery();

        try {
            $hasEnough = null !== $query->setMaxResults(1)->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            $hasEnough = false;
        }

        return $hasEnough;
    }
}