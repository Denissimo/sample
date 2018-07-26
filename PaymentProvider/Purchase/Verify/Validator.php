<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Verify;

use Plus\Proxy;
use Plus\PaymentSystem\Processing\PaymentProvider\Exceptions\MalformedVerifyException;

class Validator
{
    /**
     * @param $verifyResponse
     */
    public function validateVerify($verifyResponse)
    {
        Proxy::init()->getValidator()->validateRequired(
            $verifyResponse,
            ['verification_code'],
            null,
            MalformedVerifyException::class
        );
    }
}
