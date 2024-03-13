<?php

namespace RadishesFlight\Pay;
class PayMemberFactory
{
    /**
     * @var PaymentStrategyInterFace
     */
    public $paymentStrategy;

    /**
     * @var PaymentRefundInterFace
     */
    public $refundStrategy;

    /**
     * @var PaymentCallbackInterFace
     */
    public $paymentCallback;


    public $configDaTaOrPath;

    public function setPaymentStrategy(PaymentStrategyInterFace $paymentStrategy)
    {
        $this->paymentStrategy = $paymentStrategy;
    }

    public function processPayment($data)
    {
        return $this->paymentStrategy->pay($data);
    }

    public function setRefundStrategy(PaymentRefundInterFace $refundInterFace)
    {
        $this->refundStrategy = $refundInterFace;
    }

    public function processRefund($data)
    {
        return $this->refundStrategy->refund($data);
    }

    public function setPaymentCallback(PaymentCallbackInterFace $paymentStrategy)
    {
        $this->paymentCallback = $paymentStrategy;
    }

    public function processPaymentCallback($data = [])
    {
        return $this->paymentCallback->callback($data);
    }
}
