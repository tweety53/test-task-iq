<?php
declare(strict_types=1);

namespace App\Validator\Request;


use App\Operation\DepositOperation;
use App\Validator\Database\Constraint\AccountExistsConstraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * Class DepositFieldsValidator
 * @package App\Validator\Request
 */
class DepositFieldsValidator implements RequestFieldsValidator
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
                new Assert\EqualTo(['value' => DepositOperation::class]),
                new Assert\NotBlank(),
            ],
            'user_from' => new Assert\IsNull(),
            'user_to' => [
                new Assert\GreaterThan(['value' => 0]),
                new Assert\NotBlank(),
                new AccountExistsConstraint(),
            ],
            'amount' => [
                new Assert\GreaterThan(['value' => 0.00]),
                new Assert\NotBlank(),
            ],
        ]);

        return $validator->validate($input, $constraint, $groups);
    }
}