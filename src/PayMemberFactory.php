<?php

namespace RadishesFlight\Pay;
class PayMemberFactory
{
    /**
     * @var PaymentStrategyInterFace
     */
    public $paymentStrategy;

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

    public function setPaymentCallback(PaymentCallbackInterFace $paymentStrategy)
    {
        $this->paymentCallback = $paymentStrategy;
    }

    public function processPaymentCallback($data = [])
    {
        return $this->paymentCallback->callback($data);
    }
}
