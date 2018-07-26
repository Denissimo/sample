<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Response;

use Plus\PaymentSystem\Interfaces\Request as IRequest;
use Plus\PaymentSystem\Interfaces\Response as IResponse;
use Plus\PaymentSystem\Response;
use Plus\PaymentSystem\RedirectData\SimpleRedirectData;
use Plus\PaymentSystem\Interfaces\GateSettings as IGateSettings;
use Plus\PaymentSystem\Processing\PaymentProvider;
use Plus\Proxy;

class Builder
{
    const
        REQUEST_METHOD_POST = 'POST';

    private $gateSettings;

    public function __construct(IGateSettings $gateSettings)
    {
        $this->gateSettings = $gateSettings;
    }

    /**
     * @param IRequest $gateRequest
     * @return IResponse
     */
    public function buildResponse(IRequest $gateRequest): IResponse
    {
        return (new Response)
            ->setRedirectData(
                (new SimpleRedirectData())
                    ->setUri($gateRequest->getMerchantParams()['payment_url'])
                    ->setMethod(self::REQUEST_METHOD_POST)
                    ->setBody(
                        [
                            IRequest::GS_RETURN_URL => $gateRequest->getClientParams()[IRequest::CP_SITE_URL],
                            PaymentProvider::MERCHANT_ID => $gateRequest->getMerchantParams()[PaymentProvider::MERCHANT_ID],
                            PaymentProvider::MERCHANT_SUB_ID => $gateRequest->getMerchantParams()
                                [PaymentProvider::CALLBACK_URL_ID],
                            PaymentProvider::LOGIN => $gateRequest->getClientParams()['login'],
                            PaymentProvider::OPERATION_ID => $gateRequest->getOperationId(),
                            PaymentProvider::AMOUNT => Proxy::init()->getAmountConverter()
                                ->convertMinorToMajorUnits(
                                    $gateRequest->getAmount(),
                                    $gateRequest->getCurrency()
                                ),
                            PaymentProvider::CURRENCY => $gateRequest->getCurrency(),
                            IRequest::CP_COUNTRY => $gateRequest->getClientParams()['country'] ?? null,
                            IRequest::CP_FIRST_NAME => $gateRequest->getClientParams()['first_name'] ?? null,
                            IRequest::CP_LAST_NAME => $gateRequest->getClientParams()['last_name'] ?? null,
                            'addr_1' => $gateRequest->getClientParams()['address'] ?? null,
                            IRequest::CP_CITY => $gateRequest->getClientParams()['city'] ?? null,
                            IRequest::CP_ZIP => $gateRequest->getClientParams()['zip'] ?? null
                        ]
                    )
            )
            ->setType(IResponse::TYPE_REDIRECT)
            ->setOperationId($gateRequest->getOperationId())
            ->setCode(IResponse\Code::DEFAULT);
    }
}
