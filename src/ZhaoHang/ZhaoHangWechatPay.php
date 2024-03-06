<?php

namespace RadishesFlight\Pay\ZhaoHang;

use RadishesFlight\Pay\PaymentStrategyInterFace;
/**
 * 招行微信
 */
class ZhaoHangWechatPay extends ZhangHangAbstract implements PaymentStrategyInterFace
{
    public function pay($data)
    {
        $params = array();
        $biz_content = array();
        $biz_content["orderId"] = $data['orderId'];
        $biz_content["notifyUrl"] = $this->config['notifyUrl'];
        $biz_content["merId"] =  $this->config['merId'];
        $biz_content["payValidTime"] = $data['payValidTime']?? 900;
        $biz_content["currencyCode"] =  $this->config['currencyCode'];
        $biz_content["tradeType"] = 'JSAPI';
        $biz_content["userId"] =  $this->config['userId'];
        $biz_content["txnAmt"] = $data['amount']; //单位分
        $biz_content["mchReserved"] = $data['mchReserved'];
        $biz_content["body"] = $data['body'] ?? '';
        $biz_content["spbillCreateIp"] = $_SERVER['REMOTE_ADDR'];
        $params["encoding"] = $this->config['encoding'];
        $params["version"] =$this->config['version'];
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
        $url = $this->config['payUrl']. 'MiniAppOrderApply';
        $header = $this->getHeaderArr($sign);
        $pay = $this->curlPost($url, $params, $header);
        $pay = json_decode($pay, true);
        if ($pay['returnCode'] == 'SUCCESS') {
            if ($pay['respCode'] == 'SUCCESS' && self::validSign($pay)) {
                return $pay;
            } else {
                throw new \Exception($pay['respMsg']);
            }
        } else {
            throw new \Exception('交易失败');
        }
    }
}
