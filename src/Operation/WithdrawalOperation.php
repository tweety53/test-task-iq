<?php
declare(strict_types=1);


namespace App\Operation;


use App\Entity\Account;
use App\Entity\OperationHistory;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class WithdrawalOperation
 * @package App\Operation
 */
class WithdrawalOperation implements Operation
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
        $userId = $data['user_from'];
        $amount = $data['amount'];

        if ($amount <= '0.00'){
            return false;
        }

        $callbackResult = true;

        $callback = function () use ($userId, $amount, &$callbackResult) {
            if (!$this->em->getConnection()->ping()) {
                $this->em->getConnection()->close();
                $this->em->getConnection()->connect();
            }

            $accountRepository = $this->em->getRepository(Account::class);

            /**
             * @var Account $account
             */
            $account = $accountRepository->find(
                $userId,
                LockMode::PESSIMISTIC_WRITE
            );

            if (null === $account) {
                $this->logger->warning('User not exists in DB.', [
                    'operation' => 'withdrawal',
                    'field' => 'account',
                    'value' => $userId,
                ]);
                $callbackResult = false;
            }

            if (!$accountRepository->accountHasEnoughFunds($account->getUserId(), $amount)) {
                $this->logger->info('User doesn\'t have enough funds on account.', [
                    'operation' => 'withdrawal',
                    'field' => 'amount',
                    'userId' => $userId,
                    'value' => $amount,
                ]);
                $callbackResult = false;
            }

            $account->setAmount((string)((double)$account->getAmount() - (double)$amount));

            $historyEntry = new OperationHistory();
            $historyEntry->setAmount($amount);
            $historyEntry->setUserFrom($account);
            $historyEntry->setType(OperationHistory::TYPE_WITHDRAWAL);

            $this->em->persist($historyEntry);

            $callbackResult = true;
            return true;
        };

        try {
            $this->em->transactional($callback);
            $result = $callbackResult;
        } catch (\Throwable $e){
            $result = false;
            $this->logger->error('Something went wrong in queries', [
                'data' => $data,
                'message' => $e->getMessage(),
            ]);
        }

        return $result;
    }
}