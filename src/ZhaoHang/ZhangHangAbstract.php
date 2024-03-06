<?php

namespace RadishesFlight\Pay\ZhaoHang;

use Rtgm\sm\RtSm2;
use Rtgm\util\FormatSign;

class ZhangHangAbstract
{
    protected $config;

    public function setConfig($configDaTaOrPath)
    {
        if (!is_array($configDaTaOrPath)) {
            $configDaTaOrPath = include($configDaTaOrPath);
        }
        $this->config = $configDaTaOrPath;
    }

    final public function back($data)
    {
        $returnData = $data['biz_content'];
        $returnData = json_decode($returnData, true);
        $check = $this->ValidSign($data);
        if (!$check || !isset($returnData['orderId'])) {
            return 'FAIL';
        }
        return $check;
    }

    protected function Sign(array $array)
    {
        $array = array_filter($array);
        ksort($array);//整理数组排序
        $document = self::ToUrlParams($array);//拼接
        //加签
        $sm2 = new RtSm2('base64');
        $sign = $sm2->doSign($document, $this->config['privateKey'], '');
        return $sign;
    }

    protected function getHeaderArr($sign)
    {
        $header = array("Content-Type: application/json;charset=UTF-8", "Accept:application/json");
        $params = array();
        $params["appid"] = $this->config['appid'];
        $params["secret"] = $this->config['secret'];
        $params["sign"] = $sign;
        $params["timestamp"] = time();
        $params = array_filter($params);
        ksort($params);
        $document = self::ToUrlParams($params);
        $apisign = md5($document);
        $header[] = "appid:" . $params['appid'];
        $header[] = 'timestamp:' . $params['timestamp'];
        $header[] = 'apisign:' . $apisign;
        return $header;
    }

    /**
     * 校验签名
     */
    protected function ValidSign(array $array)
    {
        $sign = $array['sign'];
        $fs = new FormatSign();
        $sign = $fs->run($sign);
        unset($array['sign']);
        ksort($array);
        $document = self::ToUrlParams($array);
        $sm2 = new RtSm2('base64');

        $bstr = base64_decode($this->config['pubKey']);//base64解码公钥
        $pk = bin2hex($bstr);//转16进制
        //去公钥头
        $pkarr = str_replace($this->config['pubKeyHeader'], '', $pk);
        $pubkey = $pkarr;//实际公钥
        return $sm2->verifySign($document, $sign, $pubkey, '');
    }

    /**
     * @param $url
     * @param $params
     * @param $header
     * @return bool|string
     */
    protected function curlPost($url, $params, $header)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        if (!empty($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        if (is_array($params)) {
            $params = json_encode($params, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

        $data = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);//关闭cURL会话
        return $data;
    }

    protected static function ToUrlParams(array $array)
    {
        $buff = "";
        foreach ($array as $k => $v) {
            if ($v != "" && !is_array($v)) {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff, "&");
        return $buff;
    }
}
