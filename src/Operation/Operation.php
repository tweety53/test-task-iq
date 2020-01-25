<?php
declare(strict_types=1);

namespace App\Operation;


/**
 * Interface Operation
 * @package App\Operation
 */
interface Operation
{
    public const OPERATION_CLASS_MAP = [
        'deposit' => DepositOperation::class,
        'withdrawal' => WithdrawalOperation::class,
        'transfer' => TransferOperation::class,
    ];

    /**
     * @param array $data
     * @return bool
     */
    public function process(array $data): bool;
}