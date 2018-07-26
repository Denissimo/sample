<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Callback;

use Plus\PaymentSystem\Processing\PaymentProvider;
use Plus\PaymentSystem\CallbackData\SimpleCallbackData;
use Plus\Service\RequestConstructor\WithCallbackData;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Plus\PaymentSystem\Processing\PaymentProvider\Purchase\CacheKeeper;

class Constructor extends WithCallbackData
{
    /**
     * @inheritdoc
     */
    protected function loadRequest(): HttpRequest
    {
        return (new CacheKeeper())->loadRequest(
            $this->callbackRequest->request->all()[PaymentProvider::OPERATION_ID]
        );
    }

    /**
     * @inheritdoc
     */
    protected function prepareCallbackData()
    {
        return
            (new SimpleCallbackData())->setData(
                $this->callbackRequest->request->all()
            );
    }
}
