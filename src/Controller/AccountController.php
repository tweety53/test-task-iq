<?php
declare(strict_types=1);

namespace App\Controller;

use App\Operation\Operation;
use App\Service\RequestFieldsValidatorService;
use App\Tools\ValidationDataPreparer;
use Enqueue\Client\ProducerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class AccountController
 * @package App\Controller
 */
class AccountController extends AbstractController
{
    private ProducerInterface $producer;

    private RequestFieldsValidatorService $requestFieldsValidator;

    /**
     * AccountController constructor.
     * @param ProducerInterface $producer
     * @param RequestFieldsValidatorService $requestFieldsValidator
     */
    public function __construct(ProducerInterface $producer, RequestFieldsValidatorService $requestFieldsValidator)
    {
        $this->producer = $producer;
        $this->requestFieldsValidator = $requestFieldsValidator;
    }

    /**
     * @param Request $request
     * @param string $operation
     * @return JsonResponse
     */
    public function performOperation(Request $request, string $operation): JsonResponse
    {
        $input = [
            'type' => Operation::OPERATION_CLASS_MAP[$operation] ?? null,
            'user_from' => $request->get('user_from'),
            'user_to' => $request->get('user_to'),
            'amount' => $request->get('amount'),
        ];

        $validatorInitialized = $this->requestFieldsValidator->init($operation);

        if (!$validatorInitialized) {
            return $this->json([
                'errors' => [
                    'fatal' => 'Operation "' . $operation . '" not supported for this app.',
                ],
            ])->setStatusCode(400);
        }

        $validationData = $this->requestFieldsValidator->validate($input);

        if ($validationData->count() > 0) {
            return $this->json(ValidationDataPreparer::prepareForJsonResponse($validationData))->setStatusCode(400);
        }

        $this->producer->sendCommand('account_operation', json_encode($input, JSON_THROW_ON_ERROR, 512));

        return $this->json(['result' => 'OK'])->setStatusCode(200);
    }
}