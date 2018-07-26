<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Callback;

use GuzzleHttp\Exception\TransferException;
use Plus\PaymentSystem\Processing\PaymentProvider;
use Plus\PaymentSystem\Processing\PaymentProvider\Purchase\CacheKeeper;
use Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Callback\Response\Builder;
use Plus\Proxy;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Silex\ControllerCollection;
use Plus\Controller\PSCallController;
use Symfony\Component\HttpFoundation\Response;
use Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Verify\Verificator;
use Plus\PaymentSystem\Processing\PaymentProvider\Exceptions\DataMismatchException;
use Plus\PaymentSystem\Processing\PaymentProvider\Exceptions\MalformedVerifyException;
use Plus\PaymentSystem\Interfaces\Response as IResponse;
use Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Callback\Response\CallbackResponse;

class Controller extends PSCallController
{

    /** @inheritdoc */
    protected function getRequestConstructorService(HttpRequest $request)
    {
        return new Constructor($request);
    }

    /** @inheritdoc */
    protected function validate(HttpRequest $request)
    {
        $this->getValidator()->validateRequired(
            $request->request->all(),
            [PaymentProvider::OPERATION_ID]
        );
    }

    /** @inheritdoc */
    protected function route(ControllerCollection $controllers)
    {
        $controllers->post('/', [$this, 'callbackProcess']);
    }

    /**
     * @param HttpRequest $request
     * @return Response
     */
    public function callbackProcess(HttpRequest $request)
    {
        Proxy::init()->getApp()->error(
            function () {
                return new Response();
            }
        );
        (new Validator())->validateIp();
        try {
            (new Validator())->validateCallback($request->request->all());
            $cacheData = (new CacheKeeper())
            ->loadExtraData(
                $request->request->all()[PaymentProvider::OPERATION_ID]
            );
            (new Validator())->validateCacheData($request->request->all(), $cacheData);
            Proxy::init()->getAutoCallbacker()->callbackCancel(
                PaymentProvider::PREFIX_CANCEL_KEY . $request->request->all()[PaymentProvider::OPERATION_ID]
            );
            $verifyResponse = $this->verify($request);
            $this->gateCallback($request, $verifyResponse->getResponseCode(), $verifyResponse->getVerificationCode());
        } catch (DataMismatchException $e) {
            Proxy::init()->getAutoCallbacker()->callbackCancel(
                PaymentProvider::PREFIX_CANCEL_KEY . $request->request->all()[PaymentProvider::OPERATION_ID]
            );
            $this->gateCallback($request, IResponse\Code::INVALID_AMOUNT_OR_CURRENCY, 0);
        } catch (MalformedVerifyException $e) {
            Proxy::init()->getAutoCallbacker()->callbackCancel(
                PaymentProvider::PREFIX_CANCEL_KEY . $request->request->all()[PaymentProvider::OPERATION_ID]
            );
            $this->gateErrroCallback($request, IResponse\Code::MALFORMED_RESPONSE);
        } catch (TransferException $e) {
            Proxy::init()->getAutoCallbacker()->callbackCancel(
                PaymentProvider::PREFIX_CANCEL_KEY . $request->request->all()[PaymentProvider::OPERATION_ID]
            );
            $this->gateErrroCallback($request, IResponse\Code::NETWORK_ERROR);
        }
        return new Response();
    }

    /**
     * @param HttpRequest $request
     * @param int $responseCode
     * @param string | int $verifyCode
     */
    private function gateCallback(HttpRequest $request, int $responseCode, $verifyCode)
    {
        $gateRequest = $this->getRequestConstructorService($request)->run();
        $gateSettings = $this->getGateSettings($gateRequest);
        $callBackResponse = new CallbackResponse($request);
        $Response  =(new Builder())
            ->buildResponse($gateRequest, $callBackResponse, $responseCode, $verifyCode);
        Proxy::init()->getGateCallback()->run(
            $Response,
            $gateSettings
        );
    }

    /**
     * @param HttpRequest $request
     * @param int $code
     */
    private function gateErrroCallback(HttpRequest $request, int $code)
    {
        $gateRequest = $this->getRequestConstructorService($request)->run();
        $gateSettings = $this->getGateSettings($gateRequest);
        $Response = (new Builder())
            ->buildErrorResponse($gateRequest, $code);
        Proxy::init()->getGateCallback()->run(
            $Response,
            $gateSettings
        );
    }

    /**
     * @param HttpRequest $request
     * @return PaymentProvider\Purchase\Verify\Response\VerifyResponse
     */
    private function verify(HttpRequest $request)
    {
        return (new Verificator())->verify(
            $this->getRequestConstructorService($request)
                ->run()
                ->getMerchantParams()[PaymentProvider::URL_VERIFY],
            $request->request->all()
        );
    }

    /**
     * @param $gateRequest
     * @return \Plus\PaymentSystem\Interfaces\GateSettings
     */
    protected function getGateSettings($gateRequest)
    {
        return $this->getGateSettingsService($gateRequest)->prepare();
    }
}
