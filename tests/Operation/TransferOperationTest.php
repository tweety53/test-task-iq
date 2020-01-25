<?php
declare(strict_types=1);

namespace App\Tests\Operation;


use App\Entity\Account;
use App\Entity\OperationHistory;
use App\Operation\TransferOperation;
use App\Service\OperationProcessorService;
use App\Service\RequestFieldsValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class TransferOperationTest
 * @package App\Tests\Operation
 */
class TransferOperationTest extends KernelTestCase
{
    private TransferOperation $operation;

    private OperationProcessorService $service;

    private EntityManagerInterface $em;

    /**
     * @test
     * @covers \App\Operation\TransferOperation::process
     */
    public function correctTransfer()
    {
        $this->initClass();

        $result = $this->service->process([
            'type' => TransferOperation::class,
            'user_from' => 1,
            'user_to' => 2,
            'amount' => '1.33',
        ]);

        static::assertTrue($result);

        /**
         * @var Account $account1
         * @var Account $account2
         */
        $account1 = $this->em->getRepository(Account::class)->find(1);
        $account2 = $this->em->getRepository(Account::class)->find(2);

        static::assertEquals('98.67', $account1->getAmount());
        static::assertEquals('51.33', $account2->getAmount());

        /**
         * @var OperationHistory $operationHistory
         */
        $operationHistory = $this->em->getRepository(OperationHistory::class)->findBy([], ['id' => 'DESC'], 1)[0];
        static::assertEquals($account1, $operationHistory->getUserFrom());
        static::assertEquals($account2, $operationHistory->getUserTo());
        static::assertEquals(OperationHistory::TYPE_TRANSFER, $operationHistory->getType());
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
        $this->operation = new TransferOperation($this->service->getEm());
        $this->service->setOperation($this->operation);
    }

    /**
     * @test
     * @covers \App\Operation\TransferOperation::process
     */
    public function transferFromUnexistentAccount()
    {
        $this->initClass();

        $result = $this->service->process([
            'type' => TransferOperation::class,
            'user_from' => 3,
            'user_to' => 2,
            'amount' => '1.33',
        ]);

        static::assertFalse($result);
    }

    /**
     * @test
     * @covers \App\Operation\TransferOperation::process
     */
    public function transferToUnexistentAccount()
    {
        $this->initClass();

        $result = $this->service->process([
            'type' => TransferOperation::class,
            'user_from' => 1,
            'user_to' => 3,
            'amount' => '1.33',
        ]);

        static::assertFalse($result);
    }

    /**
     * @test
     * @covers \App\Operation\TransferOperation::process
     */
    public function transferMoreThanIsOnAccount()
    {
        $this->initClass();

        $result = $this->service->process([
            'type' => TransferOperation::class,
            'user_from' => 1,
            'user_to' => 2,
            'amount' => '666.66',
        ]);

        static::assertFalse($result);
    }

    /**
     * @test
     * @covers \App\Operation\TransferOperation::process
     */
    public function transferWithNegativeAmount()
    {
        $this->initClass();

        $result = $this->service->process([
            'type' => TransferOperation::class,
            'user_from' => 1,
            'user_to' => 2,
            'amount' => '-1.00',
        ]);

        static::assertFalse($result);
    }
}