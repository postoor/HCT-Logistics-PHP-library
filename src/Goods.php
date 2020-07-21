<?php

namespace postoor\HCTLogistics;

use GuzzleHttp\Client;

class Goods
{
    private $desType = 'DES-CBC';

    protected $key;

    protected $ivValue;

    protected $vValue;

    public function __construct(
        $ivValue,
        $vValue,
        $url = 'https://www.hct.com.tw/phone/searchGoods_Main_Xml.ashx',
        $key = null
    ) {
        $this->ivValue = $ivValue;

        $this->vValue = $vValue;

        $this->url = $url;

        $this->key = $key ?: (new \DateTime('-17 days'))->format('Ymd');
    }

    /**
     * Query Goods Tracking status.
     */
    public function queryGoods(array $goodsNOs)
    {
        $data = [];

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><qrylist></qrylist>');
        foreach ($goodsNOs as $goodsNO) {
            $xml->addChild('order', ' ')->addAttribute('orderid', $goodsNO);
        }
        $xmlString = $xml->asXML();

        $encryptString = $this->encrypt($xmlString);

        $client = new Client();
        $response = $client->request('GET', "{$this->url}?no={$encryptString}&v={$this->vValue}");

        if ($response->getStatusCode() != 200) {
            throw new \Exception('Connected Failed');
        }

        $decryptContent = $response->getBody();

        $content = $this->decrypt($decryptContent);

        if (!$content) {
            throw new \Exception('Not Response');
        }

        $responseXML = new \SimpleXMLElement($content);

        foreach ($responseXML->children() as $order) {
            $orderID = (string) $order['ordersid'];

            foreach ($order->order as $detail) {
                $data[$orderID]['detail'][] = [
                    'wrkTime' => (string) $detail['wrktime'],
                    'statusString' => (string) $detail['status'],
                ];
            }
        }

        return $data;
    }

    /**
     * Encrypt String.
     *
     * @return string
     */
    public function encrypt(string $xmlString)
    {
        return base64_encode(openssl_encrypt($xmlString, $this->desType, $this->key, OPENSSL_RAW_DATA, $this->ivValue));
    }

    /**
     * Decrypt String.
     *
     * @return string
     */
    public function decrypt(string $decryptString)
    {
        return openssl_decrypt(base64_decode($decryptString), $this->desType, $this->key, OPENSSL_RAW_DATA, $this->ivValue);
    }
}
