<?php

namespace RadishesFlight\Pay;

interface PaymentRefundInterFace
{
    public function refund($data);

    public function refundQuery($data);

}
