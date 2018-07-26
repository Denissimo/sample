<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider\Payout\Response;

use Plus\PaymentSystem\Processing\PaymentProvider;
use Plus\PaymentSystem\Processing\PaymentProvider\Exceptions\MalformedResponseException;
use Plus\Proxy;
use Symfony\Component\Validator\Constraints as Assert;

class Validator
{
    /**
     * @param $response
     */
    public function validateResponse($response)
    {
        Proxy::init()->getValidator()->validate(
            $response,
            [
                PaymentProvider::ID => new Assert\NotBlank(),
                PaymentProvider::TYPE => new Assert\NotBlank(),
                PaymentProvider::ERROR_CODE => new Assert\NotNull()
            ],
            null,
            MalformedResponseException::class
        );

        Proxy::init()->getValidator()->validate(
            $response[PaymentProvider::TYPE],
            new Assert\Choice(
                ['choices' => [PaymentProvider::STATUS_SUCCESS, PaymentProvider::STATUS_DECLINE, PaymentProvider::STATUS_ERROR]]
            ),
            null,
            MalformedResponseException::class
        );
    }
}
