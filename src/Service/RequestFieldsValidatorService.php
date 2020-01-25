<?php
declare(strict_types=1);

namespace App\Service;


use App\Validator\Request\DepositFieldsValidator;
use App\Validator\Request\RequestFieldsValidator;
use App\Validator\Request\TransferFieldsValidator;
use App\Validator\Request\WithdrawalFieldsValidator;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class RequestFieldsValidatorService
 * @package App\Service
 */
class RequestFieldsValidatorService
{
    private const REQUEST_FIELDS_VALIDATORS_MAP = [
        'deposit' => DepositFieldsValidator::class,
        'withdrawal' => WithdrawalFieldsValidator::class,
        'transfer' => TransferFieldsValidator::class,
    ];

    private ValidatorInterface $validator;

    private RequestFieldsValidator $requestFieldsValidator;

    /**
     * RequestFieldsValidatorService constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param string $operation
     * @return bool
     */
    public function init(string $operation): bool
    {
        $requestFieldsValidatorClassName = self::REQUEST_FIELDS_VALIDATORS_MAP[$operation] ?? null;

        if (null === $requestFieldsValidatorClassName) {
            return false;
        }

        $this->requestFieldsValidator = new $requestFieldsValidatorClassName();
        return true;
    }

    /**
     * @param array $input
     * @return ConstraintViolationListInterface
     */
    public function validate(array $input): ConstraintViolationListInterface
    {
        return $this->requestFieldsValidator->validate($input, $this->validator);
    }


}