<?php
declare(strict_types=1);

namespace App\Operation;


use App\Entity\Account;
use App\Entity\OperationHistory;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class TransferOperation
 * @package App\Operation
 */
class TransferOperation implements Operation
{
    private EntityManagerInterface $em;

    private LoggerInterface $logger;

    /**
     * DepositOperation constructor.
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function process(array $data): bool
    {
        $userIdFrom = $data['user_from'];
        $userIdTo = $data['user_to'];
        $amount = $data['amount'];

        if ($amount <= '0.00'){
            return false;
        }

        $callbackResult = true;

        $callback = function () use ($userIdFrom, $userIdTo, $amount, &$callbackResult) {

            if (!$this->em->getConnection()->ping()) {
                $this->em->getConnection()->close();
                $this->em->getConnection()->connect();
            }

            $accountRepository = $this->em->getRepository(Account::class);

            /**
             * @var Account $accountFrom
             * @var Account $accountTo
             */
            $accountFrom = $accountRepository->find(
                $userIdFrom,
                LockMode::PESSIMISTIC_WRITE
            );

            if (null === $accountFrom) {
                $this->logger->warning('User not exists in DB.', [
                    'operation' => 'transfer',
                    'field' => 'accountFrom',
                    'value' => $userIdFrom,
                ]);
                $callbackResult = false;
            }

            if (!$accountRepository->accountHasEnoughFunds($accountFrom->getUserId(), $amount)) {
                $this->logger->info('User doesn\'t have enough funds on account.', [
                    'operation' => 'transfer',
                    'field' => 'amount',
                    'userId' => $userIdFrom,
                    'value' => $amount,
                ]);
                $callbackResult = false;
            }

            $accountTo = $accountRepository->find(
                $userIdTo,
                LockMode::PESSIMISTIC_WRITE
            );

            if (null === $accountTo) {
                $this->logger->warning('User not exists in DB.', [
                    'operation' => 'transfer',
                    'field' => 'accountTo',
                    'value' => $userIdTo,
                ]);
                $callbackResult = false;
            }

            $accountFrom->setAmount((string)((double)$accountFrom->getAmount() - (double)$amount));
            $accountTo->setAmount((string)((double)$accountTo->getAmount() + (double)$amount));

            $historyEntry = new OperationHistory();
            $historyEntry->setAmount($amount);
            $historyEntry->setUserFrom($accountFrom);
            $historyEntry->setUserTo($accountTo);
            $historyEntry->setType(OperationHistory::TYPE_TRANSFER);

            $this->em->persist($historyEntry);

            $callbackResult = true;
            return true;
        };

        try {
            $result = $this->em->transactional($callback);
        } catch (\Throwable $e) {
            $result = false;
            $this->logger->error('Something went wrong in queries', [
                'data' => $data,
                'message' => $e->getMessage(),
            ]);
        }

        return $result;
    }
}