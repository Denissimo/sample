<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Request;

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
            [
                IRequest::FIELD_AMOUNT => $gateRequest->getAmount(),
                IRequest::FIELD_CURRENCY => $gateRequest->getCurrency()
            ],
            [
                IRequest::FIELD_AMOUNT,
                IRequest::FIELD_CURRENCY
            ]
        );
        Proxy::init()->getValidator()->validateRequired(
            $gateRequest->getMerchantParams(),
            [
                PaymentProvider::URL_VERIFY,
                PaymentProvider::URL_PAYMENT,
                PaymentProvider::URL_PAYOUT,
                PaymentProvider::MERCHANT_ID,
                PaymentProvider::MERCHANT_PASS,
                PaymentProvider::CALLBACK_URL_ID
            ]
        );

        Proxy::init()->getValidator()->validateRequired(
            $gateRequest->getClientParams(),
            [
                IRequest::CP_LOGIN,
                IRequest::CP_FIRST_NAME,
                IRequest::CP_LAST_NAME
            ]
        );
    }
}
