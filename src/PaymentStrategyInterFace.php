<?php

namespace Radish\Pay;

interface PaymentStrategyInterFace
{
    public function pay($data);
}
