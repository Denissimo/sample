<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider;

use Plus\PaymentSystem\Interfaces\Request as IRequest;
use Plus\PaymentSystem\Interfaces\Response as IResponse;
use Plus\PaymentSystem\Interfaces\GateSettings as IGateSettings;
use Plus\PaymentSystem\Processing\PaymentProvider;
use Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Request\Validator;
use Plus\PaymentSystem\Processing\PaymentProvider\Purchase\CacheKeeper;
use Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Response\Builder;
use Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Callback\Response\Builder as AutoCallbackBuilder;
use Plus\Proxy;

class Purchase
{
    const
        CALLBACK_TIMEOUT = 2 * 60 * 60;

    const
        CHECK_EVENT_KEY = 'check_event_key';

    /** @var IGateSettings */
    private $gateSettings;


    /**
     * Purchase constructor.
     * @param IGateSettings $gateSettings
     */
    public function __construct(IGateSettings $gateSettings)
    {
        $this->gateSettings = $gateSettings;
    }

    /**
     * @param IRequest $gateRequest
     * @return IResponse
     */
    public function purchase(IRequest $gateRequest): IResponse
    {
        (new Validator())->validateRequest($gateRequest);

        Proxy::init()->getAutoCallbacker()
            ->callbackRegister(
                (new AutoCallbackBuilder())->buildErrorResponse(
                    $gateRequest,
                    IResponse\Code::AUTO
                ),
                $this->gateSettings,
                self::CALLBACK_TIMEOUT,
                PaymentProvider::PREFIX_CANCEL_KEY . $gateRequest->getOperationId()
            );

        (new CacheKeeper())
            ->cacheRequest($gateRequest)
            ->cacheExtraData($gateRequest);

        return (new Builder($this->gateSettings))->buildResponse($gateRequest);
    }
}
