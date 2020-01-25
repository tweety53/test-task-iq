<?php
declare(strict_types=1);

namespace App\Operation;

use App\Entity\Account;
use App\Entity\OperationHistory;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class DepositOperation
 * @package App\Operation
 */
class DepositOperation implements Operation
{
    private EntityManagerInterface $em;

    /**
     * DepositOperation constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function process(string $amount, ?Account $accountFrom, ?Account $accountTo): bool
    {
        if (null === $accountTo) {
            return false;
        }

        $accountTo->setAmount((string)((double)$amount + (double)$accountTo->getAmount()));

        $historyEntry = new OperationHistory();
        $historyEntry->setAmount($amount);
        $historyEntry->setUserTo($accountTo);
        $historyEntry->setType(OperationHistory::TYPE_DEPOSIT);

        $this->em->persist($historyEntry);

        return true;
    }
}