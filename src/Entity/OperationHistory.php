<?php

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
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="smallint", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $amount;

    /**
     * @var \Account
     *
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_from", referencedColumnName="user_id")
     * })
     */
    private $userFrom;

    /**
     * @var \Account
     *
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_to", referencedColumnName="user_id")
     * })
     */
    private $userTo;


}
