<?php
declare(strict_types=1);

namespace App\Tools;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class ValidationDataPreparer
 * @package App\Tools
 */
class ValidationDataPreparer
{
    /**
     * @param ConstraintViolationListInterface $violationList
     * @return array
     */
    public static function prepareForJsonResponse(ConstraintViolationListInterface $violationList): array
    {
        $result = [];

        /**
         * @var ConstraintViolation $item
         */
        foreach ($violationList as $item) {
            $result['errors'][] = [
                $item->getPropertyPath() => $item->getMessage(),
            ];
        }

        return $result;
    }
}