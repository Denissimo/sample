<?php

namespace Plus\PaymentSystem\Processing;

use Plus\PaymentSystem\Interfaces\Processing\PaymentMethods\Purchase;
use Plus\PaymentSystem\Processing;
use Plus\PaymentSystem\Interfaces\Processing\PaymentMethods\Payout;
use Plus\PaymentSystem\Processing\PaymentProvider\Payout as IDPayout;
use Plus\PaymentSystem\Processing\PaymentProvider\Purchase as IDPurchase;
use Plus\PaymentSystem\Interfaces\Request as IRequest;
use Plus\PaymentSystem\Interfaces\Response as IResponse;

class PaymentProvider extends Processing implements Payout, Purchase
{
    const
        PaymentProvider_CALLBACK_PATH = 'PaymentProvider/CallbackPurchase',
        IDEBIT_CALLBACK_PATH = 'IDebit/CallbackPurchase',
        URL_PAYMENT = 'payment_url',
        URL_VERIFY = 'verification_url',
        URL_PAYOUT = 'payout_url',
        URL_CHECK = 'check_url',
        PREFIX_CACHE_REQUEST = 'PaymentProvider_',
        PREFIX_CACHE_FIELDS = 'PaymentProvider_Fields_Purchase',
        PREFIX_CANCEL_KEY = 'PaymentProvider_Cancel_Key_',
        MERCHANT_ID = 'merchant_id',
        MERCHANT_PASS = 'merchant_pass',
        MERCHANT_SUB_ID = 'merchant_sub_id',
        CALLBACK_URL_ID = 'callback_url_id',
        ACCOUNT = 'user_id',
        LOGIN = 'merchant_user_id',
        OPERATION_ID = 'merchant_txn_num',
        QUERY_TYPE = 'query_type',
        QUERY_SDATE = 'query_sdate',
        QUERY_EDATE = 'query_edate',
        ID = 'txn_num',
        TYPE = 'txn_status',
        AMOUNT = 'txn_amount',
        CURRENCY = 'txn_currency',
        CODE = 'code',
        DATE = 'date',
        TIME = 'time',
        TIMEZONE = 'Canada/Central',
        ERROR_CODE = 'error_code',
        EXPLODE_DELIMETER = ','
    ;

    const
        STATUS_SUCCESS = 'S',
        STATUS_DECLINE = 'R',
        STATUS_ERROR = 'X',
        STATUS_WAITING = '',
        VALUE_OK = 0;

    public static $whiteIpList = [
        '35.182.57.121'
    ];

    /**
     * @var array
     */
    public static $statuses = [
        PaymentProvider::STATUS_SUCCESS => IResponse::TYPE_SUCCESS,
        PaymentProvider::STATUS_DECLINE => IResponse::TYPE_DECLINE,
        PaymentProvider::STATUS_ERROR => IResponse::TYPE_DECLINE
    ];

    /**
     * @param IRequest $gateRequest
     * @return IResponse
     */
    public function payout(IRequest $gateRequest): IResponse
    {
        return (new IDPayout($this->gateSettings))->payout($gateRequest);
    }

    /**
     * @param IRequest $gateRequest
     * @return IResponse
     */
    public function purchase(IRequest $gateRequest): IResponse
    {
        return (new IDPurchase($this->gateSettings))->purchase($gateRequest);
    }
}
