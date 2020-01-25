<?php
declare(strict_types=1);

namespace App\Operation;


use App\Entity\Account;
use App\Entity\OperationHistory;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class TransferOperation
 * @package App\Operation
 */
class TransferOperation implements Operation
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
        if (null === $accountTo || null === $accountFrom) {
            return false;
        }

        $accountFrom->setAmount((string)((double)$accountFrom->getAmount() - (double)$amount));
        $accountTo->setAmount((string)((double)$accountTo->getAmount() + (double)$amount));

        $historyEntry = new OperationHistory();
        $historyEntry->setAmount($amount);
        $historyEntry->setUserFrom($accountFrom);
        $historyEntry->setUserTo($accountTo);
        $historyEntry->setType(OperationHistory::TYPE_TRANSFER);

        $this->em->persist($historyEntry);

        return true;
    }
}