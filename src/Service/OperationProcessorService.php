<?php
declare(strict_types=1);

namespace App\Service;


use App\Operation\Operation;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class OperationProcessorService
 * @package App\Service
 */
class OperationProcessorService
{
    private Operation $operation;

    private EntityManagerInterface $em;

    private LoggerInterface $logger;

    /**
     * OperationProcessorService constructor.
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEm(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param Operation $operation
     * @return OperationProcessorService
     */
    public function setOperation(Operation $operation): OperationProcessorService
    {
        $this->operation = $operation;
        return $this;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function process(array $data): bool
    {
        return $this->operation->process($data);
    }
}