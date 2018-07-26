<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider\Payout\Request;

use Plus\PaymentSystem\Interfaces\Request as IRequest;
use Plus\PaymentSystem\Processing\PaymentProvider;
use Plus\Proxy;

class Builder
{

    const
        REQUEST_METHOD_POST = 'POST';

    /**
     * @param IRequest $gateRequest
     * @return array
     */
    public function buildPayout(IRequest $gateRequest)
    {
        return [
            PaymentProvider::MERCHANT_ID => $gateRequest->getMerchantParams()[PaymentProvider::MERCHANT_ID],
            PaymentProvider::MERCHANT_PASS => $gateRequest->getMerchantParams()[PaymentProvider::MERCHANT_PASS],
            PaymentProvider::ACCOUNT => $gateRequest->getClientParams()[IRequest::CP_ACCOUNT],
            PaymentProvider::LOGIN => $gateRequest->getClientParams()[IRequest::CP_LOGIN],
            PaymentProvider::OPERATION_ID => $gateRequest->getOperationId(),
            'txn_type' => 'F',
            PaymentProvider::AMOUNT => Proxy::init()->getAmountConverter()->convertMinorToMajorUnits(
                $gateRequest->getAmount(),
                $gateRequest->getCurrency()
            ),
            PaymentProvider::CURRENCY => $gateRequest->getCurrency()
        ];
    }
}
