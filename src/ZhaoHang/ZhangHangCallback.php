<?php

namespace RadishesFlight\Pay\ZhaoHang;


use RadishesFlight\Pay\PaymentCallbackInterFace;

/**
 * 招行回调
 */
class ZhangHangCallback extends ZhangHangAbstract implements PaymentCallbackInterFace
{
    public function callback($data)
    {
        return $this->back($data);
    }
}
