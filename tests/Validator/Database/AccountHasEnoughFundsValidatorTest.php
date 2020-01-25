<?php
declare(strict_types=1);

namespace App\Tests\Validator\Database;


use App\Repository\AccountRepository;
use App\Validator\Database\AccountHasEnoughFundsValidator;
use App\Validator\Database\Constraint\AccountHasEnoughFundsConstraint;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * Class AccountHasEnoughFundsValidatorTest
 * @package App\Tests\Validator\Database
 */
class AccountHasEnoughFundsValidatorTest extends ConstraintValidatorTestCase
{
    private MockObject $accountRepoMock;

    /**
     * @test
     * @covers \App\Validator\Database\AccountHasEnoughFundsValidator::validate
     */
    public function withValidAccount()
    {
        $this->accountRepoMock->method('accountHasEnoughFunds')->willReturn(true);
        $this->validator->validate(1, new AccountHasEnoughFundsConstraint(['userId' => 1, 'amount' => '1.11']));
        $this->assertNoViolation();
    }

    /**
     * @test
     * @covers \App\Validator\Database\AccountHasEnoughFundsValidator::validate
     */
    public function withInvalidAccount()
    {
        $this->accountRepoMock->method('accountHasEnoughFunds')->willReturn(false);
        $this->validator->validate(1, new AccountHasEnoughFundsConstraint(['userId' => 1, 'amount' => '111.11']));
        $this->buildViolation('User with provided ID does not have enough funds for this operation.')
            ->assertRaised();
    }

    /**
     * @return AccountHasEnoughFundsValidator
     */
    protected function createValidator(): AccountHasEnoughFundsValidator
    {
        $this->accountRepoMock = $this->createMock(AccountRepository::class);

        $emMock = $this->createMock(EntityManagerInterface::class);
        $emMock->method('getRepository')->willReturn($this->accountRepoMock);

        return new AccountHasEnoughFundsValidator($emMock);
    }
}