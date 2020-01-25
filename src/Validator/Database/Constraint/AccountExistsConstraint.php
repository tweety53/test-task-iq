<?php
declare(strict_types=1);

namespace App\Validator\Database\Constraint;


use App\Validator\Database\AccountExistsValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Class AccountExistsConstraint
 * @package App\Validator\Database\Constraint
 */
class AccountExistsConstraint extends Constraint
{
    public string $message = 'User with provided ID does not exists in DB.';

    /**
     * @return string
     */
    public function validatedBy(): string
    {
        return AccountExistsValidator::class;
    }

    /**
     * @return string|null
     */
    public function getDefaultOption()
    {
        return 'value';
    }

}