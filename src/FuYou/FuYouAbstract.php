<?php

namespace Radish\Pay\FuYou;

class FuYouAbstract
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
        $result = file_get_contents("php://input");
        $result = json_decode($result, true);
        if ($result['resp_code'] == '0000') {
            //鍙湁resp_code涓�0000鐨勬椂鍊欙紝鎵嶆湁message銆�
            $decrypted = $this->privateDecryptRsa($result['message']);
            if ($decrypted) {
                $decrypted = json_decode($decrypted, true);
                $decrypted['order_amt'] = bcdiv($decrypted['order_amt'], 100, 2);
                return $decrypted;
            }
        }
        return [];
    }

    public function publicEncryptRsa($plainData = '')
    {
        if (!is_string($plainData)) {
            return null;
        }
        $encrypted = '';
        $partLen = $this->getPublicKenLen() / 8 - 11;
        $plainData = str_split($plainData, $partLen);
        $publicPEMKey = $this->getPublicKey();
        foreach ($plainData as $chunk) {
            $partialEncrypted = '';
            $encryptionOk = openssl_public_encrypt($chunk, $partialEncrypted, $publicPEMKey, OPENSSL_PKCS1_PADDING);
            if ($encryptionOk === false) {
                return false;
            }
            $encrypted .= $partialEncrypted;
        }
        return base64_encode($encrypted);
    }

    private function getPublicKenLen()
    {
        $pub_id = openssl_get_publickey($this->getPublicKey());
        return openssl_pkey_get_details($pub_id)['bits'];
    }

    private function getPublicKey()
    {
        $public_key = $this->config['rsa_public_key'];
        $pubic_pem = chunk_split($public_key, 64, "\n");
        $pubic_pem = "-----BEGIN PUBLIC KEY-----\n" . $pubic_pem . "-----END PUBLIC KEY-----\n";
        return $pubic_pem;
    }

    public function privateDecryptRsa($data = '')
    {
        if (!is_string($data)) {
            return null;
        }
        $decrypted = '';

        $partLen = $this->getPrivateKenLen() / 8;
        $data = str_split(base64_decode($data), $partLen);

        $privatePEMKey = $this->getPrivateKey();

        foreach ($data as $chunk) {
            $partial = '';
            $decryptionOK = openssl_private_decrypt($chunk, $partial, $privatePEMKey, OPENSSL_PKCS1_PADDING);
            if ($decryptionOK === false) {
                return false;
            }
            $decrypted .= $partial;
        }
        return $decrypted;
    }

    private function getPrivateKenLen()
    {
        $pub_id = openssl_get_privatekey($this->getPrivateKey());
        return openssl_pkey_get_details($pub_id)['bits'];
    }

    private function getPrivateKey()
    {
        $private_key = $this->config['rsa_private_key'];
        $private_pem = chunk_split($private_key, 64, "\n");
        $private_pem = "-----BEGIN PRIVATE KEY-----\n" . $private_pem . "-----END PRIVATE KEY-----\n";
        return $private_pem;
    }
}
