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

    /**
     * @var PaymentResultInterFace
     */
    public $resultInterFace;



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

    public function processRefundQeury($data)
    {
        return $this->refundStrategy->refundQuery($data);
    }

    public function setPaymentCallback(PaymentCallbackInterFace $paymentStrategy)
    {
        $this->paymentCallback = $paymentStrategy;
    }

    public function processPaymentCallback($data = [])
    {
        return $this->paymentCallback->callback($data);
    }

    public function setResultParams(PaymentResultInterFace $resultInterFace)
    {
        $this->resultInterFace = $resultInterFace;
    }

    public function processResult($data = [])
    {
        return $this->resultInterFace->resultQuery($data);
    }
}
