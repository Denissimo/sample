<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider;

use Plus\PaymentSystem\Interfaces\Request as IRequest;
use Plus\PaymentSystem\Interfaces\Response as IResponse;
use Plus\PaymentSystem\Interfaces\GateSettings as IGateSettings;
use Plus\PaymentSystem\Processing\PaymentProvider;
use GuzzleHttp\Exception\TransferException;
use Plus\PaymentSystem\Processing\PaymentProvider\Exceptions\MalformedResponseException;
use Plus\PaymentSystem\Processing\PaymentProvider\Payout\Request\Validator as RequestValidator;
use Plus\PaymentSystem\Processing\PaymentProvider\Payout\Response\Validator as ResponseValidator;
use Plus\PaymentSystem\Processing\PaymentProvider\Payout\Request\Builder as RequestBuilder;
use Plus\PaymentSystem\Processing\PaymentProvider\Payout\Response\Builder as ResponseBuilder;
use Plus\PaymentSystem\Response;
use Plus\Proxy;

class Payout
{
    /** @var IGateSettings */
    private $gateSettings;

    /**
     * Payout constructor.
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
    public function payout(IRequest $gateRequest): IResponse
    {
        (new RequestValidator())->validateRequest($gateRequest);
        $request = (new RequestBuilder())->buildPayout($gateRequest);
        try {
            /** @var  $httpResponse array | null */
            $httpResponse = $this->sendRequest(
                $gateRequest->getMerchantParams()[PaymentProvider::URL_PAYOUT],
                $request
            );
            (new ResponseValidator())->validateResponse($httpResponse);
            $Response = (new ResponseBuilder())->buildResponse($gateRequest, $httpResponse);
        } catch (TransferException $e) {
            $Response = (new Response())->setType(IResponse::TYPE_WAITING)
                ->setOperationId($gateRequest->getOperationId())
                ->setCode(IResponse\Code::NETWORK_ERROR);
        } catch (MalformedResponseException $e) {
            $Response = (new Response())->setType(IResponse::TYPE_WAITING)
                ->setOperationId($gateRequest->getOperationId())
                ->setCode(IResponse\Code::MALFORMED_RESPONSE);
        }
        return $Response;
    }

    /**
     * @param string $url
     * @param $data
     * @return array
     */
    private function sendRequest(string $url, $data): array
    {
        parse_str(
            (string)Proxy::init()->getHttpClient()
                ->withFormDataFormatter([PaymentProvider::MERCHANT_PASS])
                ->request(RequestBuilder::REQUEST_METHOD_POST, $url, ['form_params' => $data])->getBody(),
            $parsedResult
        );
        return $parsedResult;
    }
}
