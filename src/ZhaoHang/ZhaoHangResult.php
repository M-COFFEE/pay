<?php

namespace RadishesFlight\Pay\ZhaoHang;

use Exception;
use RadishesFlight\Pay\PaymentRefundInterFace;
use RadishesFlight\Pay\PaymentResultInterFace;

class ZhaoHangResult extends ZhangHangAbstract implements PaymentResultInterFace
{
    public function resultQuery($data)
    {
        $params = array();
        $biz_content = array();
        $biz_content["merId"] = $this->config['merId'];
        $biz_content["orderId"] = $data['orderId']??"";//被查询交易的商户订单号，此字段和平台订单号字段至少要上送一个，若两个都上送，则以平台订单号为准
        $biz_content["cmbOrderId"] = $data['cmbOrderId']??""; //被查询交易招行订单号，此字段和商户订单号字段至少要上送一个，若两个都上送，则以此字段为准
        $params["signMethod"] = $this->config['signMethod'];
        $params["encoding"] = $this->config['encoding'];
        $params["version"] = $this->config['version'];
        $biz_content = array_filter($biz_content);
        ksort($biz_content);
        $params["biz_content"] = json_encode($biz_content, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        //签名
        $params = array_filter($params);
        ksort($params);
        $sign = $this->Sign($params); //签名
        $params["sign"] = $sign;
        ksort($params);
        $url = $this->config['payUrl'] . 'orderquery';
        $header = $this->getHeaderArr($sign);
        $pay = $this->curlPost($url, $params, $header);
        $pay = json_decode($pay, true);
        if (!empty($pay) && $pay['returnCode'] == 'SUCCESS') {
            if ($pay['respCode'] == 'SUCCESS' && $this->validSign($pay)) {
                return $pay;
            } else {
                throw new Exception($pay['respMsg']);
            }
        } else {
            throw new Exception('交易失败');
        }
    }
}
