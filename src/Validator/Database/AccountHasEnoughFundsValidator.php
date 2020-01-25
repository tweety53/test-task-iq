<?php
declare(strict_types=1);

namespace App\Validator\Database;

use App\Entity\Account;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class AccountHasEnoughFundsValidator
 * @package App\Validator\Database
 */
class AccountHasEnoughFundsValidator extends ConstraintValidator
{
    /**
     * @var AccountRepository
     */
    private $accountRepository;

    /**
     * AccountExistsValidator constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->accountRepository = $em->getRepository(Account::class);
    }

    /**
     * @param string $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint): void
    {
        $userId = $constraint->getUserId();
        $amount = $constraint->getAmount();

        if (!$this->accountRepository->accountHasEnoughFunds((int)$userId, $amount)) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}