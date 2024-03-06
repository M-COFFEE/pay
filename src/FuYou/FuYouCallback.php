<?php

namespace RadishesFlight\Pay\FuYou;


use RadishesFlight\Pay\PaymentCallbackInterFace;

/**
 * 富友回调
 */
class FuYouCallback extends FuYouAbstract implements PaymentCallbackInterFace
{
    public function callback($data)
    {
        return $this->back($data);
    }
}
