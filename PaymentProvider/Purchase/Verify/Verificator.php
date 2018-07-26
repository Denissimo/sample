<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Verify;

use Plus\Proxy;
use Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Verify\Response\VerifyResponse;

class Verificator
{
    const
        REQUEST_METHOD_POST = 'POST';
    /**
     * @param $url
     * @param $request
     * @return VerifyResponse
     */
    public function verify($url, $request)
    {
        $httpQuery = (new Builder())->buildVerifyRequest($request);
        $verifyresp = (string)$this->sendRequest($url, ['body' => $httpQuery])->getBody();
        parse_str(
            $verifyresp,
            $parsedBody
        );
        (new Validator())->validateVerify(
            $parsedBody
        );

        return new VerifyResponse($parsedBody);
    }

    /**
     * @param string $url
     * @param $data
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function sendRequest(string $url, $data)
    {
        return Proxy::init()->getHttpClient()->request(
            self::REQUEST_METHOD_POST,
            $url,
            $data
        );
    }
}
