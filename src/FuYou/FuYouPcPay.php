<?php

namespace Radish\Pay\FuYou;


use GuzzleHttp\Client;
use Radish\Pay\PaymentCallbackInterFace;
use Radish\Pay\PaymentStrategyInterFace;

/**
 * 富友支付-pc扫码
 */
class FuYouPcPay extends FuYouAbstract implements PaymentStrategyInterFace
{
    public function pay($data)
    {
        //鎶ユ枃浣擄紝array
        $message_body = [
            'mchnt_cd' => $this->config['mchnt_cd'],//商户代码
            'order_date' => date("Ymd"),
            'order_id' => $data['order_id'],
            'order_amt' => $data['order_amt'],
            'order_pay_type' => $data['type'],
            'back_notify_url' => $this->config['back_notify_url'],//回调地址
            'goods_name' => '药品',
            'goods_detail' => $data['order_id'],
            'appid' => '',
            'openid' => '',
            'ver' => '1.0.0'
        ];
        $message = $this->publicEncryptRsa(json_encode($message_body));
        $client = new Client(['verify' => false]);
        $url = $this->config['pay_pc_url'];
        $res = $client->request('POST', $url, [
            'json' => [
                'mchnt_cd' => $message_body['mchnt_cd'],
                'message' => $message
            ]
        ]);
        $result = $res->getBody()->getContents();
        $decrypted = "";
        if ($result) {
            $result = json_decode($result, true);
            if ($result['resp_code'] == '0000') {
                //鍙湁resp_code涓�0000鐨勬椂鍊欙紝鎵嶆湁message銆�
                $decrypted = $this->privateDecryptRsa($result['message']);
                if ($decrypted) {
                    $decrypted = json_decode($decrypted, true);
                }
            }
        }
        return $decrypted;
    }
}
