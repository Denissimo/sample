<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider\Payout\Response;

use Plus\PaymentSystem\Interfaces\Request as IRequest;
use Plus\PaymentSystem\Interfaces\Response as IResponse;
use Plus\PaymentSystem\Processing\PaymentProvider;
use Plus\PaymentSystem\Response;
use Plus\PaymentSystem\ExternalData\SimpleExternalData;

class Builder
{
    /**
     * @param IRequest $gateRequest
     * @param array $response
     * @return IResponse
     */
    public function buildResponse(IRequest $gateRequest, array $response): IResponse
    {
        return
            (new Response())->setType($this->getType($response))
                ->setOperationId($gateRequest->getOperationId())
                ->setCode(IResponse\Code::DEFAULT)
                ->setExternalData(
                    (new SimpleExternalData())
                        ->setId($response[PaymentProvider::ID])
                        ->setCode($this->getCode($response))
                        ->setMessage(
                            $this->getMessage(
                                $response
                            )
                        )
                );
    }

    /**
     * @param array $response
     * @return string
     */
    private function getType(array $response)
    {
        return PaymentProvider::$statuses[$response[PaymentProvider::TYPE]];
    }

    /**
     * @param array $response
     * @return string
     */
    private function getCode(array $response) : string
    {
        if (empty($response[PaymentProvider::ERROR_CODE])) {
            $response[PaymentProvider::ERROR_CODE] = $response[PaymentProvider::TYPE] == PaymentProvider::STATUS_SUCCESS ?
                PaymentProvider::STATUS_SUCCESS : PaymentProvider::STATUS_DECLINE;
        }
        return $response[PaymentProvider::ERROR_CODE];
    }


    /**
     * @param array $response
     * @return string
     */
    public function getMessage(array $response) : string
    {
        if (in_array($this->getCode($response), array_keys(self::$messages))) {
            return self::$messages[$this->getCode($response)];
        } else {
            return 'Unknown Error';
        }
    }

    /**
     * @var array
     */
    private static $messages = [
        'F002' => 'Content of the request does not comply with the specification.',
        'F003' => 'The merchant fails IDS’ authentication.',
        'F004' => 'The user id provided by merchant does not exist within IDS.',
        'F005' => 'The merchant user id field is missing.',
        'F006' => 'The transaction type is incorrect.',
        'F007' => 'The transaction amount is incorrect.',
        'F008' => 'The currency of the transaction is not supported.',
        'F009' => 'The merchant transaction number is not unique.',
        'F010' => 'System internal error.',
        'F011' => 'System internal error.',
        'F012' => 'System internal error.',
        'F013' => 'System internal error.',
        'F014' => 'System internal error.',
        'F015' => 'System internal error.',
        'F016' => 'The customer to whom the payout is sent has been suspended or blocked 
 by IDS due to suspicious or fraudulent activities.',
        'F017' => 'The transaction is declined to avoid overdraft from a merchant’s account. 
 sA merchant’s balance with IDS must be able to cover a payout in order for the transaction to be processed.',
        'S' => 'Successful',
        'R' => 'decline'
    ];
}
