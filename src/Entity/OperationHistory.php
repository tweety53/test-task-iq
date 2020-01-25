<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OperationHistory
 *
 * @ORM\Table(name="operation_history", indexes={@ORM\Index(name="user_to", columns={"user_to"}), @ORM\Index(name="user_from", columns={"user_from"})})
 * @ORM\Entity
 */
class OperationHistory
{
    public const TYPE_DEPOSIT = 1;
    public const TYPE_WITHDRAWAL = 2;
    public const TYPE_TRANSFER = 3;

    public const TYPE_UNKNOWN = 4;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="smallint", nullable=false)
     */
    private int $type;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2, nullable=false)
     */
    private string $amount;

    /**
     * @var Account|null
     *
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_from", referencedColumnName="user_id")
     * })
     */
    private ?Account $userFrom;

    /**
     * @var Account|null
     *
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_to", referencedColumnName="user_id")
     * })
     */
    private ?Account $userTo;

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return OperationHistory
     */
    public function setType(int $type): OperationHistory
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return OperationHistory
     */
    public function setAmount(string $amount): OperationHistory
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return Account
     */
    public function getUserFrom(): Account
    {
        return $this->userFrom;
    }

    /**
     * @param Account $userFrom
     * @return OperationHistory
     */
    public function setUserFrom(Account $userFrom): OperationHistory
    {
        $this->userFrom = $userFrom;
        return $this;
    }

    /**
     * @return Account
     */
    public function getUserTo(): Account
    {
        return $this->userTo;
    }

    /**
     * @param Account $userTo
     * @return OperationHistory
     */
    public function setUserTo(Account $userTo): OperationHistory
    {
        $this->userTo = $userTo;
        return $this;
    }


}
