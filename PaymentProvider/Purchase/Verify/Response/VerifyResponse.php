<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Verify\Response;

use Plus\PaymentSystem\Interfaces\Response as IResponse;

class VerifyResponse
{
    const FIELD_CODE = 'verification_code';

    /**
     * @var string | int
     */
    private $verificationCode;

    /**
     * @var string | null
     */
    private $verificationMessage = null;

    /**
     * @var bool
     */
    private $success;

    /**
     * @var int
     */
    private $responseCode = IResponse\Code::DEFAULT;

    /**
     * VerifyResponse constructor.
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->verificationCode = $response[self::FIELD_CODE];
        $this->success = (!$this->verificationCode);
        if (!$this->success) {
            $this->responseCode = IResponse\Code::MALFORMED_RESPONSE;
            $this->verificationMessage = self::$messages[$this->verificationCode] ?? null;
        }
    }

    /**
     * @return int
     */
    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    /**
     * @return int|string
     */
    public function getVerificationCode()
    {
        return $this->verificationCode;
    }

    /**
     * @var array
     */
    public static $messages = [
            'C001' => 'Invalid HTTP request',
            'C002' => 'Invalid content',
            'C003' => 'Invalid Transaciton ID',
            'C004' => 'Notification not found',
            'C005' => 'Notification does not match',
            'C006' => 'Generic error'
        ];
}
