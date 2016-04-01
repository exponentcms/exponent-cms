<?php
//Created by Jason Henry of Yourarcadescript.com
//Based on the work by Ranjith http://renjith.co.in/
//free to use, this is a rough release so check back soon

class TranslateMe
{
    private $apiKey = '';
//    private $apiURL = 'https://api.datamarket.azure.com/Data.ashx/Bing/MicrosoftTranslator/v1/';
    private $apiURL = 'https://api.datamarket.azure.com/Bing/MicrosoftTranslator/v1/';
    public $treatedURL = '';
    public $response = '';
    public $status = '';
    public $error = '';
    public $oldText = '';
    public $newText = '';
    public $curlError = '';

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    private function createUrl($input, $to, $from, $type)
    {
        $text = (!empty($input)) ? 'Text=' . urlencode("'" . $input . "'") : '';
        $to = (!empty($to)) ? $to = '&To=' . urlencode("'" . $to . "'") : '';
        $from = (!empty($from)) ? '&From=' . urlencode("'" . $from . "'") : '';
        $format = '$format=json';
        $top = '$top=100';
        $params = $text . $from . $to . '&' . $format . '&' . $top;
        if ($type == 'Translate') {
            $this->treatedURL = $this->apiURL . $type . '?' . $params;
        } elseif ($type == 'GetLanguagesForTranslation') {
            $this->treatedURL = $this->apiURL . $type . '?' . $top . '&' . $format;
        }
        return true;
    }

    private function processTranslation()
    {
        $init = curl_init();
        curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($init, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt(
            $init,
            CURLOPT_HTTPHEADER,
            array(
                "Authorization: Basic " . base64_encode($this->apiKey . ":" . $this->apiKey)
            )
        );
        curl_setopt($init, CURLOPT_URL, $this->treatedURL);
        curl_setopt($init, CURLOPT_FOLLOWLOCATION, true);
        $data = curl_exec($init);
        if (curl_errno($init)) {
            $this->curlError = curl_error($init);
        }
        curl_close($init);
        return $data;
    }

    public function translate($text, $from = "en", $to, $type = "Translate")
    {
        $this->oldText = $text;
        $this->status = $this->createURL($this->oldText, $to, $from, $type);
        $this->response = $this->processTranslation();
        $data = json_decode($this->response, true);
        $this->newText = $data['d']['results'][0]['Text'];
        empty($data);
        return $this->newText;
    }
}

?>
