<?php
declare(strict_types=1);

namespace App\Validator\Database\Constraint;


use App\Validator\Database\AccountHasEnoughFundsValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Class AccountHasEnoughFundsConstraint
 * @package App\Validator\Database\Constraint
 */
class AccountHasEnoughFundsConstraint extends Constraint
{
    public string $message = 'User with provided ID does not have enough funds for this operation.';

    /**
     * @var int
     */
    public $userId;

    /**
     * @var string
     */
    public $amount;

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return AccountHasEnoughFundsValidator::class;
    }

    public function getDefaultOption()
    {
        return 'value';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }
}