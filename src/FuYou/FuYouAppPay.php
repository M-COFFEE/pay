<?php

namespace RadishesFlight\Pay\FuYou;

use GuzzleHttp\Client;
use Radish\Pay\PaymentStrategyInterFace;

/**
 * 富友支付-app
 */
class FuYouAppPay extends FuYouAbstract implements PaymentStrategyInterFace
{
    protected $data;

    public function pay($data)
    {
        $message_body = [
            'mchnt_cd' => $this->config['mchnt_cd'],
            'order_date' => date("Ymd"),
            'order_id' => $data['order_id'],
            'ver' => '1.0.0',
        ];
        $message = $this->publicEncryptRsa(json_encode($message_body));
        $client = new Client(['verify' => false]);
        $url = $this->config['pay_app_url'];
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
        $this->data = array_merge($data, $decrypted);
        return [
            'mchntCd' => $decrypted['mchnt_cd'],
            'orderDate' => $decrypted['order_date'],
            'orderAmt' => $data['order_amt'],
            'orderId' => $decrypted['order_id'],
            'backNotifyUrl' => $this->config['back_notify_url'],
            'goodsName' => '药品',
            'goodsDetail' => $data['order_id'],
            'orderTmStart' => date("YmdHid"),
            'orderTmEnd' => date("YmdHid", time() + 60),
            'order_token' => $decrypted['order_token'],
        ];
    }
}
