<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Account
 *
 * @ORM\Table(name="account")
 * @ORM\Entity
 */
class Account
{
    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2, nullable=false, options={"default"="0.00"})
     */
    private string $amount = '0.00';

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
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
     * @return Account
     */
    public function setAmount(string $amount): Account
    {
        $this->amount = $amount;
        return $this;
    }

}
