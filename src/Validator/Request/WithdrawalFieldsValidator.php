<?php
declare(strict_types=1);

namespace App\Validator\Request;


use App\Operation\WithdrawalOperation;
use App\Validator\Database\Constraint\AccountExistsConstraint;
use App\Validator\Database\Constraint\AccountHasEnoughFundsConstraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class WithdrawalFieldsValidator
 * @package App\Validator\Request
 */
class WithdrawalFieldsValidator implements RequestFieldsValidator
{
    /**
     * @param array $input
     * @param ValidatorInterface $validator
     * @return ConstraintViolationListInterface
     */
    public function validate(array $input, ValidatorInterface $validator): ConstraintViolationListInterface
    {
        $groups = new Assert\GroupSequence(['Default', 'custom']);

        $constraint = new Assert\Collection([
            'type' => [
                new Assert\EqualTo(['value' => WithdrawalOperation::class]),
                new Assert\NotBlank(),
            ],
            'user_from' => [
                new Assert\GreaterThan(['value' => 0]),
                new Assert\NotBlank(),
                new AccountExistsConstraint(),
                new AccountHasEnoughFundsConstraint([
                    'userId' => (int)$input['user_from'],
                    'amount' => (string)$input['amount'],
                ]),
            ],
            'user_to' => new Assert\IsNull(),
            'amount' => [
                new Assert\GreaterThan(['value' => 0.00]),
                new Assert\NotBlank(),
            ],
        ]);

        return $validator->validate($input, $constraint, $groups);
    }
}