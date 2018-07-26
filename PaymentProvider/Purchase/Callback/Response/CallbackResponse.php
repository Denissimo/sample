<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Callback\Response;

use Plus\PaymentSystem\Processing\PaymentProvider;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Plus\PaymentSystem\Interfaces\Response as IResponse;

class CallbackResponse
{
    private $account;
    private $id;
    private $merchantId;
    private $login;
    private $operationId;
    private $amount;
    private $currency;
    private $type;
    private $errorCode;
    private $responseType;


    public function __construct(HttpRequest $request)
    {
        $this->account = $request->request->all()[PaymentProvider::ACCOUNT];
        $this->id = $request->request->all()[PaymentProvider::ID];
        $this->merchantId = $request->request->all()[PaymentProvider::MERCHANT_ID];
        $this->login = $request->request->all()[PaymentProvider::LOGIN];
        $this->operationId = $request->request->all()[PaymentProvider::OPERATION_ID];
        $this->amount = $request->request->all()[PaymentProvider::AMOUNT];
        $this->currency = $request->request->all()[PaymentProvider::CURRENCY];
        $this->type = $request->request->all()[PaymentProvider::TYPE];
        $this->responseType = $this->getStatus($this->type);
        $this->errorCode = empty($request->request->all()[PaymentProvider::ERROR_CODE]) ?
            $this->errorCodeValue() : $request->request->all()[PaymentProvider::ERROR_CODE];
    }

    /**
     * @return string
     */
    private function errorCodeValue()
    {
        if ($this->responseType == IResponse::TYPE_SUCCESS) {
            $this->errorCode = PaymentProvider::STATUS_SUCCESS;
        } else {
            $this->errorCode = PaymentProvider::STATUS_DECLINE;
        }
        return $this->errorCode;
    }

    /**
     * @return mixed
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return mixed
     */
    public function getOperationId()
    {
        return $this->operationId;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getResponseType(): string
    {
        return $this->responseType;
    }

    /**
     * @param string $type
     * @return string
     */
    private function getStatus(string $type)
    {
        return PaymentProvider::$statuses[$type];
    }
}
