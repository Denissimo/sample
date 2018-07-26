<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Response;

use Plus\PaymentSystem\Processing\PaymentProvider;

class ErrorMessage
{
    /**
     * @param $errorCode
     * @return mixed|null|string
     */
    public function getMessage($errorCode)
    {
        if (!$errorCode) {
            return null;
        } elseif (in_array($errorCode, array_keys(self::$messages))) {
            return self::$messages[$errorCode];
        } else {
            return 'Unknown Error';
        }
    }

    /** @var array */
    private static $messages = [
        PaymentProvider::STATUS_DECLINE => 'decline',
        PaymentProvider::STATUS_SUCCESS => 'Successful',
        '1' => 'Transaction is terminated. It fails IDS\' IP validation.',
        '2' => 'Transaction is terminated. The customer\'s IDS account is blocked or suspended',
        '3' => 'Risk management subsystem returns 
        suspicious/fraudulent information on the bank account. The customer\'s IDS account is blocked as a result.',
        '4' => 'Transaction is terminated. The amount exceeds IDS transaction velocity limits.',
        '7' => 'Transaction is terminated. Failed bank account verification attempts by the customer 
        exceed the maximal allowed.',
        '8' => 'The customer cancels transaction after failed bank account verification.',
        '9' => 'Transaction is terminated. 
        Failed identity verification attempts by the customer exceed the maximal allowed.',
        '10' => 'Transaction is terminated. 
        Risk management subsystem rejects the transaction due to bank account information mismatch.',
        '12' => 'Transaction is terminated. The customer has too many failed login attempts. 
        The account is temporarily blocked.',
        '13' => 'Transaction is terminated. 
        Risk management subsystem rejects the transaction due to negative information on the bank account.',
        '14' => 'Transaction is terminated. 
        Risk management subsystem returns suspicious/fraudulent information on the identity. 
        The customer’s sign­‐up request is declined.',
        '15' => 'Transaction is terminated. 
        Customer’s personal information from the merchant mismatches that from IDS. 
        The cross verification is turned on only when the merchant requires such feature.',
        '16' =>
        'Transaction is terminated. There is not sufficient fund to cover the transaction.',
        '17' =>
        'Transaction is terminated. The cross-­currency transaction is not supported.',
        '19' =>
        'Transaction is terminated. The country where the consumer resides is blocked by system.',
        '20' =>
        'Transaction is terminated. The consumer must verify his/her bank account before transacting.',
        '22' =>
        'Transaction is terminated. 
        Risk management subsystem returns suspicious/fraudulent information on the device/IP address.',
        '24' =>
        'Transaction is terminated. Country is not supported.',
        '25' =>
        'Transaction is terminated. Country is not supported by merchant. (global transactions only).',
        '26' =>
        'Transaction is terminated. Transaction abandoned by customer (global transactions only)',
        '27' =>
        'Transaction is terminated. Customer using multiple accounts detected.',
        '98' => 'Transaction is terminated. The transaction is declined (generic error)',
        '99' => 'The transaction is cancelled by the customer.'

    ];
}
