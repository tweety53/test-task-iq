<?php
declare(strict_types=1);

namespace App\Tests\Repository;


use App\Entity\Account;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class AccountRepositoryTest
 * @package App\Tests\Repository
 */
class AccountRepositoryTest extends KernelTestCase
{
    /**
     * @var AccountRepository
     */
    private $accountRepo;

    /**
     * @test
     * @covers \App\Repository\AccountRepository::isAccountExists
     */
    public function isAccountExistsForExistsAccount()
    {
        $this->init();
        static::assertTrue($this->accountRepo->isAccountExists(1));
    }

    /**
     * @test
     * @covers \App\Repository\AccountRepository::isAccountExists
     */
    public function isAccountExistsForUnexistentAccount()
    {
        $this->init();
        static::assertFalse($this->accountRepo->isAccountExists(3));
    }

    /**
     * @test
     * @covers \App\Repository\AccountRepository::accountHasEnoughFunds
     */
    public function accountHasEnoughFundsWhenFundsIsEnough()
    {
        $this->init();
        static::assertTrue($this->accountRepo->accountHasEnoughFunds(1, '99.99'));
    }

    /**
     * @test
     * @covers \App\Repository\AccountRepository::accountHasEnoughFunds
     */
    public function accountHasEnoughFundsWhenFundsNotEnough()
    {
        $this->init();
        static::assertFalse($this->accountRepo->accountHasEnoughFunds(1, '111.11'));
    }

    private function init(): void
    {
        if (!self::$booted) {
            self::bootKernel();
        }

        $container = self::$container;

        $em = $container->get(EntityManagerInterface::class);

        $this->accountRepo = $em->getRepository(Account::class);
    }
}