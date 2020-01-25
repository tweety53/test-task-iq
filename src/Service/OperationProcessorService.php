<?php
declare(strict_types=1);

namespace App\Service;


use App\Entity\Account;
use App\Operation\Operation;
use App\Tools\ValidationDataPreparer;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class OperationProcessorService
 * @package App\Service
 */
class OperationProcessorService
{
    private Operation $operation;

    private EntityManagerInterface $em;

    private LoggerInterface $logger;

    private RequestFieldsValidatorService $fieldsValidatorService;

    /**
     * OperationProcessorService constructor.
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     * @param RequestFieldsValidatorService $fieldsValidatorService
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger, RequestFieldsValidatorService $fieldsValidatorService)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->fieldsValidatorService = $fieldsValidatorService;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEm(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @param Operation $operation
     * @return OperationProcessorService
     */
    public function setOperation(Operation $operation): OperationProcessorService
    {
        $this->operation = $operation;
        return $this;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function process(array $data): bool
    {
        $amount = $data['amount'] ?? '0.00';
        $accountFromId = $data['user_from'] ?? 0;
        $accountToId = $data['user_to'] ?? 0;

        $validatorInitialized = $this->fieldsValidatorService->init(array_search($data['type'], Operation::OPERATION_CLASS_MAP, true));

        if (!$validatorInitialized) {
            $this->logger->warning('Validator initializing error', [$data]);
            return false;
        }

        $validationData = $this->fieldsValidatorService->validate($data);

        if ($validationData->count() > 0) {
            $this->logger->warning('Data validation error.', [
                'data' => $data,
                'errors' => ValidationDataPreparer::prepareForJsonResponse($validationData),
            ]);

            return false;
        }

        $callbackResult = true;

        $callback = function () use ($amount, $accountFromId, $accountToId, &$callbackResult) {

            $accountRepository = $this->em->getRepository(Account::class);

            if (!$this->em->getConnection()->ping()) {
                $this->em->getConnection()->close();
                $this->em->getConnection()->connect();
            }

            /**
             * @var Account $accountFrom
             * @var Account $accountTo
             */
            $accountFrom = $accountRepository->find(
                $accountFromId,
                LockMode::PESSIMISTIC_WRITE
            );

            $accountTo = $accountRepository->find(
                $accountToId,
                LockMode::PESSIMISTIC_WRITE
            );

            $callbackResult = $this->operation->process($amount, $accountFrom, $accountTo);

            return true;
        };

        try {
            $this->em->transactional($callback);
            $result = $callbackResult;
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