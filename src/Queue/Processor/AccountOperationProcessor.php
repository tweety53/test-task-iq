<?php
declare(strict_types=1);

namespace App\Queue\Processor;

use App\Service\OperationProcessorService;
use Enqueue\Client\CommandSubscriberInterface;
use Interop\Queue\Context;
use Interop\Queue\Message;
use Interop\Queue\Processor;

/**
 * Class OperationProcessor
 * @package App\Queue\Processor
 */
class AccountOperationProcessor implements Processor, CommandSubscriberInterface
{
    private OperationProcessorService $processorService;

    /**
     * AccountOperationProcessor constructor.
     * @param OperationProcessorService $processorService
     */
    public function __construct(OperationProcessorService $processorService)
    {
        $this->processorService = $processorService;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedCommand()
    {
        return [
            'processorName' => 'account_operation',
            'queueName' => 'account-operation',
            'queueNameHardcoded' => true,
            'exclusive' => true,
        ];
    }

    /**
     * @inheritDoc
     */
    public function process(Message $message, Context $context)
    {
        $msgData = json_decode($message->getBody(), true, 512, JSON_THROW_ON_ERROR);

        $this->processorService->setOperation(new $msgData['type']($this->processorService->getEm()));
        return $this->processorService->process($msgData) ? self::ACK : self::REJECT;

    }
}