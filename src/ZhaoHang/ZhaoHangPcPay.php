<?php

namespace RadishesFlight\Pay\ZhaoHang;

use Exception;
use RadishesFlight\Pay\PaymentStrategyInterFace;

/**
 * 招行pc
 */
class ZhaoHangPcPay extends ZhangHangAbstract implements PaymentStrategyInterFace
{
    public function pay($data)
    {
        $params = array();
        $biz_content = array();
        $biz_content["orderId"] = $data['orderId'];
        $biz_content["notifyUrl"] = $this->config['notifyUrl'];
        $biz_content["merId"] = $this->config['merId'];
        $biz_content["payValidTime"] = !empty($data['payValidTime']) ? $data['payValidTime'] : 1800;
        $biz_content["currencyCode"] = $this->config['currencyCode'];
        $biz_content["userId"] = $this->config['userId'];
        $biz_content["txnAmt"] = $data['txnAmt']; //单位分
        $biz_content["mchReserved"] = $data['mchReserved'] ?? "";
        $biz_content["body"] = $data['body'] ?? '';
        $params["encoding"] = $this->config['encoding'];
        $params["version"] = $this->config['version'];
        $params["signMethod"] = $this->config['signMethod'];
        $biz_content = array_filter($biz_content);
        ksort($biz_content);
        $params["biz_content"] = json_encode($biz_content, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        //签名
        $params = array_filter($params);
        ksort($params);
        $sign = $this->Sign($params); //签名
        $params["sign"] = $sign;
        ksort($params);
        $url = $this->config['payUrl'] . 'qrcodeapply';
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
