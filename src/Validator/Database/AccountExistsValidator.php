<?php
declare(strict_types=1);

namespace App\Validator\Database;


use App\Entity\Account;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


/**
 * Class AccountExistsValidator
 * @package App\Validator\Database
 */
class AccountExistsValidator extends ConstraintValidator
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
     * @param int $id
     * @param Constraint $constraint
     * @return void
     */
    public function validate($id, Constraint $constraint): void
    {
        if (!$this->accountRepository->isAccountExists((int)$id)) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}