<?php
declare(strict_types=1);

namespace App\Tests\Operation;


use App\Entity\Account;
use App\Entity\OperationHistory;
use App\Operation\DepositOperation;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class DepositOperationTest
 * @package App\Tests\Operation
 */
class DepositOperationTest extends KernelTestCase
{
    private DepositOperation $operation;

    private EntityManagerInterface $em;

    /**
     * @test
     * @covers \App\Operation\DepositOperation::process
     */
    public function correctDeposit()
    {
        $this->initClass();

        $result = $this->operation->process([
            'user_to' => 1,
            'amount' => '1.33',
        ]);

        static::assertTrue($result);

        /**
         * @var Account $account
         */
        $account = $this->em->getRepository(Account::class)->find(1);

        static::assertEquals('101.33', $account->getAmount());

        /**
         * @var OperationHistory $operationHistory
         */
        $operationHistory = $this->em->getRepository(OperationHistory::class)->findBy([], ['id' => 'DESC'], 1)[0];
        static::assertEquals($account, $operationHistory->getUserTo());
        static::assertEquals(OperationHistory::TYPE_DEPOSIT, $operationHistory->getType());
        static::assertEquals('1.33', $operationHistory->getAmount());
    }

    /**
     * @test
     * @covers \App\Operation\DepositOperation::process
     */
    public function depositForUnexistentAccount()
    {
        $this->initClass();

        $result = $this->operation->process([
            'user_to' => 3,
            'amount' => '1.33',
        ]);

        static::assertFalse($result);
    }

    private function initClass(): void
    {
        if (!self::$booted) {
            self::bootKernel();
        }

        $container = self::$container;

        $this->em = $container->get(EntityManagerInterface::class);
        $logger = $container->get(LoggerInterface::class);

        $this->operation = new DepositOperation($this->em, $logger);
    }
}