<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Callback\Response;

use Plus\PaymentSystem\Interfaces\Request as IRequest;
use Plus\PaymentSystem\Interfaces\Response as IResponse;
use Plus\PaymentSystem\Response;
use Plus\PaymentSystem\ExternalData\SimpleExternalData;
use Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Response\ErrorMessage;
use Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Verify\Response\VerifyResponse;

class Builder
{
    /**
     * @param IRequest $gateRequest
     * @param CallbackResponse $callbackResponse
     * @param int $code
     * @param string $verifyCode
     * @return IResponse
     */
    public function buildResponse(
        IRequest $gateRequest,
        CallbackResponse $callbackResponse,
        int $code,
        string $verifyCode = IResponse\Code::DEFAULT
    ) {
        return (new Response())
            ->setOperationId($gateRequest->getOperationId())
            ->setType(
                ($code == IResponse\Code::MALFORMED_RESPONSE) ?
                    Response::TYPE_WAITING : $callbackResponse->getResponseType()
            )
            ->setCode($code)
            ->setExternalData(
                (new SimpleExternalData())
                ->setAccount($callbackResponse->getAccount())
                ->setId($callbackResponse->getId())
                ->setCode($verifyCode ? $verifyCode : $callbackResponse->getErrorCode())
                ->setMessage(
                    $verifyCode ?
                        VerifyResponse::$messages[$verifyCode] :
                        (new ErrorMessage())->getMessage($callbackResponse->getErrorCode())
                )
            );
    }

    /**
     * @param IRequest $gateRequest
     * @param int $code
     * @return IResponse
     */
    public function buildErrorResponse(IRequest $gateRequest, int $code)
    {
        return (new Response())
            ->setOperationId($gateRequest->getOperationId())
            ->setType(IResponse::TYPE_WAITING)
            ->setCode($code);
    }
}
