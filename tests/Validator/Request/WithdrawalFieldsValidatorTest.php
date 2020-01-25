<?php
declare(strict_types=1);

namespace App\Tests\Validator\Request;


use App\Operation\WithdrawalOperation;
use App\Validator\Request\WithdrawalFieldsValidator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class WithdrawalFieldsValidatorTest
 * @package App\Tests\Validator\Request
 */
class WithdrawalFieldsValidatorTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    /**
     * @test
     * @covers \App\Validator\Request\WithdrawalFieldsValidator::validate
     */
    public function withValidInput()
    {
        $requestValidator = new WithdrawalFieldsValidator();
        $result = $requestValidator->validate([
            'type' => WithdrawalOperation::class,
            'user_from' => 1,
            'user_to' => null,
            'amount' => '1.00',
        ], $this->validator);

        static::assertEmpty($result);
    }

    /**
     * @test
     * @covers       \App\Validator\Request\WithdrawalFieldsValidator::validate
     * @dataProvider badInputs
     * @param array $input
     */
    public function withBadInputs(array $input)
    {
        $requestValidator = new WithdrawalFieldsValidator();
        $result = $requestValidator->validate($input, $this->validator);

        static::assertNotEmpty($result);
    }

    /**
     * Inputs with sequentially disturbed asserts inside Assert\Collection initializing
     *
     * @return array
     */
    public function badInputs(): array
    {
        return [
            [
                [
                    'type' => 'qwe',
                    'user_from' => 1,
                    'user_to' => null,
                    'amount' => '1.00',
                ],
            ],
            [
                [
                    'type' => null,
                    'user_from' => 1,
                    'user_to' => null,
                    'amount' => '1.00',
                ],
            ],
            [
                [
                    'type' => WithdrawalOperation::class,
                    'user_from' => -1,
                    'user_to' => null,
                    'amount' => '1.00',
                ],
            ],
            [
                [
                    'type' => WithdrawalOperation::class,
                    'user_from' => null,
                    'user_to' => null,
                    'amount' => '1.00',
                ],
            ],
            [
                [
                    'type' => WithdrawalOperation::class,
                    'user_from' => 3,
                    'user_to' => null,
                    'amount' => '1.00',
                ],
            ],
            [
                [
                    'type' => WithdrawalOperation::class,
                    'user_from' => 1,
                    'user_to' => null,
                    'amount' => '1111.00',
                ],
            ],
            [
                [
                    'type' => WithdrawalOperation::class,
                    'user_from' => 1,
                    'user_to' => 2,
                    'amount' => '1.00',
                ],
            ],
            [
                [
                    'type' => WithdrawalOperation::class,
                    'user_from' => 1,
                    'user_to' => null,
                    'amount' => '0.00',
                ],
            ],
            [
                [
                    'type' => WithdrawalOperation::class,
                    'user_from' => 1,
                    'user_to' => null,
                    'amount' => '-1.00',
                ],
            ],
            [
                [
                    'type' => WithdrawalOperation::class,
                    'user_from' => 1,
                    'user_to' => null,
                    'amount' => null,
                ],
            ],
        ];
    }

    protected function setUp()
    {
        parent::setUp();

        if (!self::$booted) {
            self::bootKernel();
        }

        $container = self::$container;

        $this->validator = $container->get(ValidatorInterface::class);
    }
}