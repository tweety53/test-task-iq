<?php
declare(strict_types=1);

namespace App\Tests\Validator\Database;


use App\Repository\AccountRepository;
use App\Validator\Database\AccountExistsValidator;
use App\Validator\Database\Constraint\AccountExistsConstraint;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * Class AccountExistsValidatorTest
 * @package App\Tests\Validator\Database
 */
class AccountExistsValidatorTest extends ConstraintValidatorTestCase
{
    private MockObject $accountRepoMock;

    /**
     * @test
     * @covers \App\Validator\Database\AccountExistsValidator::validate
     */
    public function withValidAccount()
    {
        $this->accountRepoMock->method('isAccountExists')->willReturn(true);
        $this->validator->validate(1, new AccountExistsConstraint());
        $this->assertNoViolation();
    }

    /**
     * @test
     * @covers \App\Validator\Database\AccountExistsValidator::validate
     */
    public function withInvalidAccount()
    {
        $this->accountRepoMock->method('isAccountExists')->willReturn(false);
        $this->validator->validate(1, new AccountExistsConstraint());
        $this->buildViolation('User with provided ID does not exists in DB.')
            ->assertRaised();
    }

    /**
     * @return AccountExistsValidator
     */
    protected function createValidator(): AccountExistsValidator
    {
        $this->accountRepoMock = $this->createMock(AccountRepository::class);

        $emMock = $this->createMock(EntityManagerInterface::class);
        $emMock->method('getRepository')->willReturn($this->accountRepoMock);

        return new AccountExistsValidator($emMock);
    }

}