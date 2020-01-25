<?php
declare(strict_types=1);

namespace App\Operation;


use App\Entity\Account;

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
     * @param string $amount
     * @param Account|null $accountFrom
     * @param Account|null $accountTo
     * @return bool
     */
    public function process(string $amount, ?Account $accountFrom, ?Account $accountTo): bool;
}