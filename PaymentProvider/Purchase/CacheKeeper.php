<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider\Purchase;

use Plus\PaymentSystem\Interfaces\Request as IRequest;
use Plus\PaymentSystem\Processing\PaymentProvider;
use Plus\PaymentSystem\Processing\PaymentProvider\Purchase;
use Plus\Proxy;

class CacheKeeper
{
    /**
     * @param IRequest $gateRequest
     * @return $this
     */
    public function cacheExtraData(IRequest $gateRequest)
    {
        Proxy::init()->getPersistentCache()->set(
            [
                PaymentProvider::LOGIN => $gateRequest->getClientParams()[IRequest::CP_LOGIN],
                PaymentProvider::CURRENCY => $gateRequest->getCurrency(),
                PaymentProvider::AMOUNT => Proxy::init()->getAmountConverter()
                    ->convertMinorToMajorUnits(
                        $gateRequest->getAmount(),
                        $gateRequest->getCurrency()
                    )
            ],
            Purchase::CALLBACK_TIMEOUT,
            PaymentProvider::PREFIX_CACHE_FIELDS . $gateRequest->getOperationId()
        );
        return $this;
    }

    public function cacheRequest(IRequest $gateRequest)
    {
        Proxy::init()->getRequestCacher()->save(
            Purchase::CALLBACK_TIMEOUT,
            PaymentProvider::PREFIX_CACHE_REQUEST . $gateRequest->getOperationId()
        );
        return $this;
    }

    /**
     * @param $operationId
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function loadRequest($operationId)
    {
        return Proxy::init()->getRequestCacher()->load(PaymentProvider::PREFIX_CACHE_REQUEST . $operationId);
    }

    /**
     * @param $operationId
     * @return array
     */
    public function loadExtraData($operationId): array
    {
        return Proxy::init()->getPersistentCache()->get(PaymentProvider::PREFIX_CACHE_FIELDS . $operationId);
    }
}
