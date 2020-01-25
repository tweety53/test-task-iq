<?php
declare(strict_types=1);

namespace App\Tests\Operation;


use App\Entity\Account;
use App\Entity\OperationHistory;
use App\Operation\WithdrawalOperation;
use App\Service\OperationProcessorService;
use App\Service\RequestFieldsValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class WithdrawalOperationTest
 * @package App\Tests\Operation
 */
class WithdrawalOperationTest extends KernelTestCase
{
    private WithdrawalOperation $operation;

    private OperationProcessorService $service;

    private EntityManagerInterface $em;

    /**
     * @test
     * @covers \App\Operation\WithdrawalOperation::process
     */
    public function correctWithdrawal()
    {
        $this->initClass();

        $result = $this->service->process([
            'type' => WithdrawalOperation::class,
            'user_from' => 1,
            'user_to' => null,
            'amount' => '1.33',
        ]);

        static::assertTrue($result);

        /**
         * @var Account $account
         */
        $account = $this->em->getRepository(Account::class)->find(1);

        static::assertEquals('98.67', $account->getAmount());

        /**
         * @var OperationHistory $operationHistory
         */
        $operationHistory = $this->em->getRepository(OperationHistory::class)->findBy([], ['id' => 'DESC'], 1)[0];
        static::assertEquals($account, $operationHistory->getUserFrom());
        static::assertEquals(OperationHistory::TYPE_WITHDRAWAL, $operationHistory->getType());
        static::assertEquals('1.33', $operationHistory->getAmount());
    }

    private function initClass(): void
    {
        if (!self::$booted) {
            self::bootKernel();
        }

        $container = self::$container;

        $this->em = $container->get(EntityManagerInterface::class);
        $logger = $container->get(LoggerInterface::class);

        $fieldsValidatorService = $container->get(RequestFieldsValidatorService::class);

        $this->service = new OperationProcessorService($this->em, $logger, $fieldsValidatorService);

        $this->operation = new WithdrawalOperation($this->service->getEm());
        $this->service->setOperation($this->operation);
    }

    /**
     * @test
     * @covers \App\Operation\WithdrawalOperation::process
     */
    public function withdrawalForUnexistentAccount()
    {
        $this->initClass();

        $result = $this->service->process([
            'type' => WithdrawalOperation::class,
            'user_from' => 3,
            'user_to' => null,
            'amount' => '1.33',
        ]);

        static::assertFalse($result);
    }

    /**
     * @test
     * @covers \App\Operation\WithdrawalOperation::process
     */
    public function withdrawalMoreThanIsOnAccount()
    {
        $this->initClass();

        $result = $this->service->process([
            'type' => WithdrawalOperation::class,
            'user_from' => 1,
            'user_to' => null,
            'amount' => '100.01',
        ]);

        static::assertFalse($result);
    }

    /**
     * @test
     * @covers \App\Operation\WithdrawalOperation::process
     */
    public function withdrawalWithNegativeAmount()
    {
        $this->initClass();

        $result = $this->service->process([
            'type' => WithdrawalOperation::class,
            'user_from' => 1,
            'user_to' => null,
            'amount' => '-1.00',
        ]);

        static::assertFalse($result);
    }
}