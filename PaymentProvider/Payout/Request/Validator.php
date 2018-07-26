<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider\Payout\Request;

use Plus\PaymentSystem\Interfaces\Request as IRequest;
use Plus\PaymentSystem\Processing\PaymentProvider;
use Plus\Proxy;

class Validator
{
    /**
     * @param IRequest $gateRequest
     */
    public function validateRequest(IRequest $gateRequest)
    {
        Proxy::init()->getValidator()->validateRequired(
            array_merge(
                $gateRequest->getClientParams(),
                [PaymentProvider::AMOUNT => $gateRequest->getAmount()],
                [PaymentProvider::CURRENCY => $gateRequest->getCurrency()]
            ),
            [
                IRequest::CP_LOGIN,
                IRequest::CP_ACCOUNT,
                PaymentProvider::AMOUNT,
                PaymentProvider::CURRENCY
            ]
        );
        Proxy::init()->getValidator()->validateRequired(
            $gateRequest->getMerchantParams(),
            [
                PaymentProvider::MERCHANT_ID,
                PaymentProvider::MERCHANT_PASS
            ]
        );
    }
}
