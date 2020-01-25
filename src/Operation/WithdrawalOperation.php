<?php
declare(strict_types=1);


namespace App\Operation;


use App\Entity\Account;
use App\Entity\OperationHistory;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class WithdrawalOperation
 * @package App\Operation
 */
class WithdrawalOperation implements Operation
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
        if (null === $accountFrom) {
            return false;
        }

        $accountFrom->setAmount((string)((double)$accountFrom->getAmount() - (double)$amount));

        $historyEntry = new OperationHistory();
        $historyEntry->setAmount($amount);
        $historyEntry->setUserFrom($accountFrom);
        $historyEntry->setType(OperationHistory::TYPE_WITHDRAWAL);

        $this->em->persist($historyEntry);

        return true;
    }
}