<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Callback;

use Plus\Proxy;
use Symfony\Component\Validator\Constraints as Assert;
use Plus\PaymentSystem\Processing\PaymentProvider;
use Plus\PaymentSystem\Processing\PaymentProvider\Exceptions\DataMismatchException;
use Plus\PaymentSystem\Processing\PaymentProvider\Exceptions\MalformedResponseException;

class Validator
{
    public function validateIp()
    {
        Proxy::init()->getValidator()->validate(
            $_SERVER['HTTP_X_REAL_IP'],
            new Assert\Choice(
                ['choices' => PaymentProvider::$whiteIpList]
            )
        );
    }

    /**
     * @param array $request
     */
    public function validateCallback(array $request)
    {
        Proxy::init()->getValidator()->validateRequired(
            $request,
            [
                PaymentProvider::ACCOUNT,
                PaymentProvider::ID,
                PaymentProvider::MERCHANT_ID,
                PaymentProvider::LOGIN,
                PaymentProvider::OPERATION_ID,
                PaymentProvider::AMOUNT,
                PaymentProvider::CURRENCY,
                PaymentProvider::TYPE
            ],
            null,
            MalformedResponseException::class
        );

        Proxy::init()->getValidator()->validate(
            $request[PaymentProvider::TYPE],
            new Assert\Choice(
                ['choices' => [PaymentProvider::STATUS_SUCCESS, PaymentProvider::STATUS_DECLINE, PaymentProvider::STATUS_ERROR]]
            ),
            null,
            MalformedResponseException::class
        );
    }

    /**
     * @param array $request
     * @param $cacheData
     */
    public function validateCacheData(array $request, $cacheData)
    {

        Proxy::init()->getValidator()->validateType(
            $request,
            [
                PaymentProvider::AMOUNT => [
                    new Assert\EqualTo(
                        $cacheData[PaymentProvider::AMOUNT]
                    )
                ],
                PaymentProvider::CURRENCY => [
                    new Assert\EqualTo($cacheData[PaymentProvider::CURRENCY]),
                ]

            ],
            null,
            DataMismatchException::class
        );

        if (!isset($request[PaymentProvider::LOGIN])) {
            return;
        }

        Proxy::init()->getValidator()->validateType(
            $request,
            [
                PaymentProvider::LOGIN => [
                    new Assert\EqualTo($cacheData[PaymentProvider::LOGIN]),
                ]
            ],
            null,
            MalformedResponseException::class
        );
    }
}
