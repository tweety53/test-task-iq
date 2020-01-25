<?php
declare(strict_types=1);

namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AccountControllerTest
 * @package App\Tests\Controller
 */
class AccountControllerTest extends WebTestCase
{
    /**
     * @test
     * @covers \App\Controller\AccountController::performOperation
     */
    public function unexistentRoute()
    {
        $client = static::createClient();

        $client->request('POST', '/account/abra', [
            'user_to' => 1,
            'amount' => '1.00',
        ]);

        static::assertEquals(400, $client->getResponse()->getStatusCode());
        static::assertStringContainsString(
            'errors', $client->getResponse()->getContent(),
            );
        static::assertStringContainsString(
            'fatal', $client->getResponse()->getContent(),
            );
    }

    /**
     * DEPOSIT ROUTE TESTS
     */

    /**
     * @test
     * @covers \App\Controller\AccountController::performOperation
     */
    public function correctDeposit()
    {
        $client = static::createClient();

        $client->request('POST', '/account/deposit', [
            'user_to' => 1,
            'amount' => '1.00',
        ]);

        static::assertEquals(200, $client->getResponse()->getStatusCode());
        static::assertEquals(
            '{"result":"OK"}',
            $client->getResponse()->getContent()
        );
    }

    /**
     * @test
     * @covers \App\Controller\AccountController::performOperation
     */
    public function depositForUnexistentUser()
    {
        $client = static::createClient();

        $client->request('POST', '/account/deposit', [
            'user_to' => 3,
            'amount' => '1.00',
        ]);

        static::assertEquals(400, $client->getResponse()->getStatusCode());
        static::assertStringContainsString(
            'errors', $client->getResponse()->getContent(),
            );
        static::assertStringContainsString(
            '[user_to]', $client->getResponse()->getContent(),
            );
    }

    /**
     * @test
     * @covers \App\Controller\AccountController::performOperation
     */
    public function depositWithBadAmount()
    {
        $client = static::createClient();

        $client->request('POST', '/account/deposit', [
            'user_to' => 1,
            'amount' => '-1.00',
        ]);

        static::assertEquals(400, $client->getResponse()->getStatusCode());
        static::assertStringContainsString(
            'errors', $client->getResponse()->getContent(),
            );
        static::assertStringContainsString(
            '[amount]', $client->getResponse()->getContent(),
            );
    }

    /**
     * @test
     * @covers       \App\Controller\AccountController::performOperation
     * @dataProvider notFullDepositParams
     * @param array $params
     */
    public function depositWithoutFullParams(array $params)
    {
        $client = static::createClient();

        $client->request('POST', '/account/deposit', $params);

        static::assertEquals(400, $client->getResponse()->getStatusCode());
        static::assertStringContainsString(
            'errors', $client->getResponse()->getContent(),
            );
    }

    /**
     * @return array
     */
    public function notFullDepositParams(): array
    {
        return [
            [
                [],
            ],
            [
                [
                    'user_to' => 1,
                ]
            ],
            [
                [
                    'amount' => '1.00',
                ],
            ],
        ];
    }

    /**
     * WITHDRAWAL ROUTE TESTS
     */

    /**
     * @test
     * @covers \App\Controller\AccountController::performOperation
     */
    public function correctWithdrawal()
    {
        $client = static::createClient();

        $client->request('POST', '/account/withdrawal', [
            'user_from' => 1,
            'amount' => '1.00',
        ]);

        static::assertEquals(200, $client->getResponse()->getStatusCode());
        static::assertEquals(
            '{"result":"OK"}',
            $client->getResponse()->getContent()
        );
    }

    /**
     * @test
     * @covers \App\Controller\AccountController::performOperation
     */
    public function withdrawalForUnexistentUser()
    {
        $client = static::createClient();

        $client->request('POST', '/account/withdrawal', [
            'user_from' => 3,
            'amount' => '1.00',
        ]);

        static::assertEquals(400, $client->getResponse()->getStatusCode());
        static::assertStringContainsString(
            'errors', $client->getResponse()->getContent(),
            );
        static::assertStringContainsString(
            '[user_from]', $client->getResponse()->getContent(),
            );
    }

    /**
     * @test
     * @covers \App\Controller\AccountController::performOperation
     */
    public function withdrawalMoreThanExistsOnAccount()
    {
        $client = static::createClient();

        $client->request('POST', '/account/withdrawal', [
            'user_from' => 1,
            'amount' => '101.00',
        ]);

        static::assertEquals(400, $client->getResponse()->getStatusCode());
        static::assertStringContainsString(
            'errors', $client->getResponse()->getContent(),
            );
        static::assertStringContainsString(
            '[user_from]', $client->getResponse()->getContent(),
            );
    }

    /**
     * @test
     * @covers       \App\Controller\AccountController::performOperation
     * @dataProvider notFullWithdrawalParams
     * @param array $params
     */
    public function withdrawalWithoutFullParams(array $params)
    {
        $client = static::createClient();

        $client->request('POST', '/account/withdrawal', $params);

        static::assertEquals(400, $client->getResponse()->getStatusCode());
        static::assertStringContainsString(
            'errors', $client->getResponse()->getContent(),
            );
    }

    /**
     * @return array
     */
    public function notFullWithdrawalParams(): array
    {
        return [
            [
                [],
            ],
            [
                [
                    'user_from' => 1,
                ]
            ],
            [
                [
                    'amount' => '1.00',
                ],
            ],
        ];
    }

    /**
     * TRANSFER ROUTE TESTS
     */

    /**
     * @test
     * @covers \App\Controller\AccountController::performOperation
     */
    public function correctTransfer()
    {
        $client = static::createClient();

        $client->request('POST', '/account/transfer', [
            'user_from' => 1,
            'user_to' => 2,
            'amount' => '1.00',
        ]);

        static::assertEquals(200, $client->getResponse()->getStatusCode());
        static::assertEquals(
            '{"result":"OK"}',
            $client->getResponse()->getContent()
        );
    }

    /**
     * @test
     * @covers \App\Controller\AccountController::performOperation
     */
    public function transferForUnexistentReceiver()
    {
        $client = static::createClient();

        $client->request('POST', '/account/transfer', [
            'user_from' => 1,
            'user_to' => 3,
            'amount' => '1.00',
        ]);

        static::assertEquals(400, $client->getResponse()->getStatusCode());
        static::assertStringContainsString(
            'errors', $client->getResponse()->getContent(),
            );
        static::assertStringContainsString(
            '[user_to]', $client->getResponse()->getContent(),
            );
    }

    /**
     * @test
     * @covers \App\Controller\AccountController::performOperation
     */
    public function transferForUnexistentSender()
    {
        $client = static::createClient();

        $client->request('POST', '/account/transfer', [
            'user_from' => 3,
            'user_to' => 1,
            'amount' => '1.00',
        ]);

        static::assertEquals(400, $client->getResponse()->getStatusCode());
        static::assertStringContainsString(
            'errors', $client->getResponse()->getContent(),
            );
        static::assertStringContainsString(
            '[user_from]', $client->getResponse()->getContent(),
            );
    }

    /**
     * @test
     * @covers \App\Controller\AccountController::performOperation
     */
    public function transferMoreThanExistsOnAccount()
    {
        $client = static::createClient();

        $client->request('POST', '/account/transfer', [
            'user_from' => 1,
            'user_to' => 2,
            'amount' => '101.00',
        ]);

        static::assertEquals(400, $client->getResponse()->getStatusCode());
        static::assertStringContainsString(
            'errors', $client->getResponse()->getContent(),
            );
        static::assertStringContainsString(
            '[user_from]', $client->getResponse()->getContent(),
            );
    }

    /**
     * @test
     * @covers       \App\Controller\AccountController::performOperation
     * @dataProvider notFullTransferParams
     * @param array $params
     */
    public function transferWithoutFullParams(array $params)
    {
        $client = static::createClient();

        $client->request('POST', '/account/transfer', $params);

        static::assertEquals(400, $client->getResponse()->getStatusCode());
        static::assertStringContainsString(
            'errors', $client->getResponse()->getContent(),
            );
    }

    /**
     * @return array
     */
    public function notFullTransferParams(): array
    {
        return [
            [
                [],
            ],
            [
                [
                    'user_from' => 1,
                    'user_to' => 2,
                ]
            ],
            [
                [
                    'amount' => '1.00',
                ],
            ],
        ];
    }
}