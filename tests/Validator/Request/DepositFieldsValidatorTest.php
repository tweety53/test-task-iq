<?php
declare(strict_types=1);

namespace App\Tests\Validator\Request;


use App\Operation\DepositOperation;
use App\Validator\Request\DepositFieldsValidator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class DepositFieldsValidatorTest
 * @package App\Tests\Validator\Request
 */
class DepositFieldsValidatorTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    /**
     * @test
     * @covers \App\Validator\Request\DepositFieldsValidator::validate
     */
    public function withValidInput()
    {
        $requestValidator = new DepositFieldsValidator();
        $result = $requestValidator->validate([
            'type' => DepositOperation::class,
            'user_from' => null,
            'user_to' => 1,
            'amount' => '1.00',
        ], $this->validator);

        static::assertEmpty($result);
    }

    /**
     * @test
     * @covers       \App\Validator\Request\DepositFieldsValidator::validate
     * @dataProvider badInputs
     * @param array $input
     */
    public function withBadInputs(array $input)
    {
        $requestValidator = new DepositFieldsValidator();
        $result = $requestValidator->validate($input, $this->validator);

        static::assertNotEmpty($result);
    }

    /**
     * Inputs with successively disturbed asserts inside Assert\Collection initializing
     *
     * @return array
     */
    public function badInputs(): array
    {
        return [
            [
                [
                    'type' => 'qwer',
                    'user_from' => null,
                    'user_to' => 1,
                    'amount' => '1.00',
                ],
            ],
            [
                [
                    'type' => null,
                    'user_from' => null,
                    'user_to' => 1,
                    'amount' => '1.00',
                ],
            ],
            [
                [
                    'type' => DepositOperation::class,
                    'user_from' => 1,
                    'user_to' => 1,
                    'amount' => '1.00',
                ],
            ],
            [
                [
                    'type' => DepositOperation::class,
                    'user_from' => null,
                    'user_to' => -1,
                    'amount' => '1.00',
                ],
            ],
            [
                [
                    'type' => DepositOperation::class,
                    'user_from' => null,
                    'user_to' => null,
                    'amount' => '1.00',
                ],
            ],
            [
                [
                    'type' => DepositOperation::class,
                    'user_from' => null,
                    'user_to' => 3,
                    'amount' => '1.00',
                ],
            ],
            [
                [
                    'type' => DepositOperation::class,
                    'user_from' => null,
                    'user_to' => 1,
                    'amount' => '0.00',
                ],
            ],
            [
                [
                    'type' => DepositOperation::class,
                    'user_from' => null,
                    'user_to' => 1,
                    'amount' => '-1.00',
                ],
            ],
            [
                [
                    'type' => DepositOperation::class,
                    'user_from' => null,
                    'user_to' => 1,
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