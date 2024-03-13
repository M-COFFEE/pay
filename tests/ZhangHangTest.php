<?php

namespace RadishesFlight\Tests;

use RadishesFlight\Pay\PayMemberFactory;
use RadishesFlight\Pay\ZhaoHang\ZhangHangCallback;
use RadishesFlight\Pay\ZhaoHang\ZhaoHangPcPay;
use RadishesFlight\Pay\ZhaoHang\ZhaoHangRefund;

class ZhangHangTest
{
    public function pay()
    {
//       招行pc扫码支付
        $pay = new PayMemberFactory();
        $payWay = new ZhaoHangPcPay();
//        //支持配置文件和数组  二选一
//        $payWay->setConfig(public_path() . 'zhanghang.php');
        $payWay->setConfig([
            "notifyUrl" => "http://www.aaa.com/s/d/callBack",//回调地址
            "merId" => "308987151550091",//商户号
            "currencyCode" => "156",//默认156，目前只支持人民币（156）
            "userId" => "N095296215",//收银员
            "encoding" => "UTF-8",//编码方式,固定为UTF-8
            "version" => "0.0.1",//版本号
            "signMethod" => "02",//"02：签名方法为SM2"
            "privateKey" => "D5F2AFA24E6BA9071B54A8C9AD735F9A1DE9C4657FA386C09B592694BC118B38",//商户私钥
            "pubKey" => "MFkwEwYHKoZIzj0CAQYIKoEcz1UBgi0DQgAE6Q+fktsnY9OFP+LpSR5Udbxf5zHCFO0PmOKlFNTxDIGl8jsPbbB/9ET23NV+acSz4FEkzD74sW2iiNVHRLiKHg==",//公钥
            "pubKeyHeader" => "3059301325g21a8648ce3d789545082a811ccf5501822d03420004",//公钥头
            "payUrl" => "https://api.cmbchina.com/polypay/v1.0/mchorders/",//pc支付地址
            "appid" => "715c56fa-b2g3-4084-91de-fffffffa18f2",
            "secret" => "4dfd569e-0s5-4499-83a7-e424325399d5",
        ]);
        $orderNumber = mt_rand(10, 100000) . time();
        $orderNumber = 'mhy' . $orderNumber;
        $payData = [
            'txnAmt' => 1,
            'orderId' => $orderNumber,//商户订单号 商户端生成，要求此订单号在整个商户下唯一
            'mchReserved' => $orderNumber//用于传输商户自定义数据，在支付结果查询和支付结果通知时原样返回（不上送则不返回此字段）
        ];
        $pay->setPaymentStrategy($payWay);
        $res = $pay->processPayment($payData);
        file_put_contents('1.txt', json_encode($res, JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
        dd(json_decode($res['biz_content'], true));
    }

    public function refund()
    {
        //退款
        $pay = new PayMemberFactory();
        $payWay = new ZhaoHangRefund();
        $payWay->setConfig(public_path() . 'zhanghang.php');
        $pay->setRefundStrategy($payWay);
        $res = $pay->processRefund([
            'origCmbOrderId' => '100424031316550099884742', //原交易平台订单号
            'txnAmt' => '1',//交易金额 分
            'refundAmt' => '1',//退款金额 分
            'orderId' => mt_rand(10, 100000) . time(),//商户订单号 商户端生成，要求此订单号在整个商户下唯一
        ]);
        dd($res);
    }

    public function callback()
    {
        //退款
        $pay = new PayMemberFactory();
        $payWay = new ZhangHangCallback();
        $payWay->setConfig(public_path() . 'zhanghang.php');
        $pay->setPaymentCallback($payWay);
        $data=[];//这个是回调数据包
        $res = $pay->processPaymentCallback($data);
        dd($res);
    }
}
