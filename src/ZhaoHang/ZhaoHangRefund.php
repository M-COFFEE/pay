<?php

namespace RadishesFlight\Pay\ZhaoHang;

use Exception;
use RadishesFlight\Pay\PaymentRefundInterFace;

class ZhaoHangRefund extends ZhangHangAbstract implements PaymentRefundInterFace
{

    public function refund($data)
    {
        $params = array();
        $biz_content = array();
        $biz_content["merId"] = $this->config['merId'];
        $biz_content["orderId"] = $data['orderId'];
        $biz_content["userId"] = $this->config['userId'];
        $biz_content["origOrderId"] = $data['origOrderId']??""; //原交易商户订单号
        $biz_content["origCmbOrderId"] = $data['origCmbOrderId']??""; //原交易平台订单号
        $biz_content["notifyUrl"] = $data['notifyUrl']??$this->config['notifyUrl'];//交易通知地址 若为空则通知到原交易的通知地址
        $biz_content["refundAmt"]=$data['refundAmt'];//退款金额
        $biz_content["refundOrigAmt"]=$data['refundOrigAmt']??'';//退单原始金额，单位为分，与refundCouponAmt同时出现
        $biz_content["refundCouponAmt"]=$data['refundCouponAmt']??'';//退单优惠金额，单位为分，与refundOrigAmt同时出现
        $biz_content["refundReason"] = $data['refundReason']??'';//退款原因
        $biz_content["currencyCode"] = $this->config['currencyCode'];//交易币种
        $biz_content["mchReserved"] = $data['mchReserved'] ?? "";//商户保留域 用于传输商户自定义数据，在退款结果查询和退款结果通知时原样返回
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
        $url = $this->config['payUrl'] . 'refund';
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
