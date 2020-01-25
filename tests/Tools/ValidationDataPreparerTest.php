<?php
declare(strict_types=1);

namespace App\Tests\Tools;


use App\Tools\ValidationDataPreparer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class ValidationDataPreparerTest
 * @package App\Tests\Tools
 */
class ValidationDataPreparerTest extends TestCase
{

    /**
     * @test
     * @covers \App\Tools\ValidationDataPreparer::prepareForJsonResponse
     */
    public function emptyCall()
    {
        static::assertEmpty(ValidationDataPreparer::prepareForJsonResponse(new ConstraintViolationList()));
    }

    /**
     * @test
     * @covers \App\Tools\ValidationDataPreparer::prepareForJsonResponse
     */
    public function simpleCall()
    {
        $expected = [
            'errors' => [
                ['a' => 'b'],
                ['c' => 'd'],
            ],
        ];

        $input = new ConstraintViolationList(
            [
                new ConstraintViolation('b','',[],'test','a',''),
                new ConstraintViolation('d','',[],'test','c',''),
            ]
        );

        static::assertEquals($expected,ValidationDataPreparer::prepareForJsonResponse($input));
    }
}