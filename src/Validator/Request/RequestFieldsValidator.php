<?php
declare(strict_types=1);

namespace App\Validator\Request;


use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Interface RequestFieldsValidator
 * @package App\Validator\Request
 */
interface RequestFieldsValidator
{
    /**
     * @param array $input
     * @param ValidatorInterface $validator
     * @return ConstraintViolationListInterface
     */
    public function validate(array $input, ValidatorInterface $validator): ConstraintViolationListInterface;
}