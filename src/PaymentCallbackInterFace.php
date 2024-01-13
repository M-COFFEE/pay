<?php

namespace Radish\Pay;

interface PaymentCallbackInterFace
{
    public function callback($data);
}
