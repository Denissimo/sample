<?php

namespace Plus\PaymentSystem\Processing\PaymentProvider\Purchase\Verify;

class Builder
{
    /**
     * @param array $params
     * @return string
     */
    public function buildVerifyRequest(array $params): string
    {
        return http_build_query(
            $params,
            null,
            '&'
        );
    }
}
