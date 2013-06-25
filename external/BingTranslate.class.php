<?php

/**
 * BingTranslateWrapper Main Class
 * 
 * @category    BingTranslateWrapper 
 * @package     BingTranslateWrapper
 * @author      Sameer Borate
 * @link        http://www.codediesel.com
 * @copyright   2011 Sameer Borate
 * @version     1.0.2
 */

class BingTranslateWrapper
{
    /**
     * URL of Bing translate
     * @var string
     */
    private $_bingTranslateBaseUrl = 'http://api.microsofttranslator.com/';
    
    /**
     * Language to translate from
     * @var string
     */
    private $_fromLang = '';
    
    /**
     * Language to translate to
     * @var string
     */
    private $_toLang = '';
    
    /**
     * Text to translate
     * @var string
     */
    private $_text = '';
    
    /**
     * Bing AppId
     * @var string
     */
    private $_appId = '';
    
    /**
     * Translated Text
     * @var string
     */
    private $_translatedText;
    
    /**
     * Service Error
     * @var string
     */
    private $_serviceError = "";
    
    /**
     * Translation success
     * @var boolean
     */
    private $_success = false;
    
    /**
     * Detected source language
     * @var string
     */
    private $_detectedSourceLanguage = "";
    
    /**
     * Cache directory to cache translation
     * @var string
     */
    private $_cache_directory = './cache/';
    
    /**
     * Enable or disable cache
     * @var bool
     */
    private $_enable_cache = false;

    
    const DETECT = 1;
    const TRANSLATE = 2;

    
    public function __construct($appId)
    {
        $this->_appId = $appId;
    }
    
    /**
     * Reset variables to be used for next query
     *
     */
    private function _reset()
    {
        $this->_fromLang = '';
        $this->_toLang = '';
        $this->_text = '';
        $this->_translatedText = '';
        $this->_postFields = '';
        $this->_serviceError = '';
        $this->_chunks = 0;
        $this->_currentChunk = 0;
        $this->_totalChunks = 0;
        $this->_detectedSourceLanguage = "";
    }
    
    
    /**
     * Process the built query using cURL and GET
     *
     * @param string POST fields
     * @return string response
     */
    private function _remoteQuery($query)
    {
        if(!function_exists('curl_init'))
        {
            return "";
        }
    
        /* Setup CURL and its options*/
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->_bingTranslateBaseUrl . $query);
        curl_setopt($ch, CURLOPT_REFERER, $this->_bingTranslateBaseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch); 

        return $response;
    }
    
    
    /**
     * Self test the class
     *
     * @return boolean
     */
    public function selfTest()
    {
        if(!function_exists('curl_init'))
        {
            echo "cURL not installed.";
        }
        else
        {
            /* Temporarily disable the cache */
            $temp = $this->_enable_cache;
            $this->_enable_cache = false;
            $testText = $this->translate("hello", "en", "fr");
            echo ($testText == "Salut") ? "Translation test Ok." : "Translation test Failed.";
            $this->_enable_cache = $temp;
        }
    }
    
    /**
     * Check if the last translation was a success
     *
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->_success;
    }
    

    /**
     * Get the detected source language, if the source is not provided
     * during query
     *
     * @return String
     */
    public function getDetectedSource()
    {
        return $this->_detectedSourceLanguage;
    }
    
    
    /**
     * Set cache status
     *
     * @param bool
     */
    public function cacheEnabled($cache)
    {
        if($cache == true || $cache == false) 
        {
            $this->_enable_cache = $cache;
        }
    }
    
    
    /**
     * Translate the given text
     * @param string $text text to translate
     * @param string $from language to translate to
     * @param string $to   language to translate from
     * @return boolean | string
     */
    public function translate($text = '', $from, $to)
    {
        $this->_success = false;

        if($text == '' || $from == '' || $to == '') 
        {
            return false;
        } 
        else 
        {
            $this->_text = $text;
            $this->_toLang = $to;
            $this->_fromLang = $from;
        }
        
        $string_signature = md5($this->_text);
        
        /* Read the data from the cache of available */
        if($this->_enable_cache == true && file_exists($this->_cache_directory . $string_signature)) 
        {
            if(is_dir($this->_cache_directory))
            {
                $handle = fopen($this->_cache_directory . $string_signature, "r");
                $contents = '';
                
                while (!feof($handle)) 
                {
                  $contents .= fread($handle, 8192);
                }
                
                fclose($handle);
                
                $this->_success = true;
                return $contents;
            } 
            else
            {
                exit("Cache directory does not exist");
            }
        }
        
        $query = "v2/Http.svc/Translate?appId=" . $this->_appId . "&text=" . urlencode($this->_text) . "&from=" . $this->_fromLang . "&to=" . $this->_toLang;

        if($this->_text != '')
        {
            $contents = $this->_remoteQuery($query);
            if(!empty($contents)) 
            {
                $xmlData = $this->_parse_xml($contents);
                
                if($xmlData->body->h1 == "Argument Exception") {   
                    $this->_reset();
                    $this->_success = false;
                    return false;
                } else { 
                    
                    $this->_translatedText = (string)$xmlData;
                    
                    /* Write the data to the cache if enabled */
                    if($this->_enable_cache == true) {
                        if(is_dir($this->_cache_directory)) {
                            $handle = fopen($this->_cache_directory . $string_signature, "w");
                            fwrite($handle, $this->_translatedText);
                            fclose($handle);
                        } else {
                            exit("Cache directory does not exist");
                        }
                    }
                    
                    $this->_success = true;
                    return $this->_translatedText;
                }
            } 
            else 
            {
                return false;
                throw new Exception('Error communicating with Bing Translate.');
            }
            
        }
        else
        {
            return false;
        }
    }
    
    
    /**
     * Return SimpleXml object
     * @param string $xml_string XML string to serialize
     * @return string
     */
    private function _parse_xml($xml_string)
    {
        return @simplexml_load_string($xml_string);
    }
    
    
    /**
     * Detect the language of the given text
     * @param string $text text of language to detect
     * @return boolean | string
     */
    public function detectLanguage($text)
    {
        if($text == '') {
            return false;
        }
        
        $this->_text = $text;
        
        $query = "v2/Http.svc/Detect?appId=" . $this->_appId . "&text=" . urlencode($this->_text);
        
        if($this->_text != '') {
            $contents = $this->_remoteQuery($query);
            $xmlData = $this->_parse_xml($contents);

            if($xmlData->body->h1 == "Argument Exception") {   
                $this->_reset();
                return false;
            } else { 
                $this->_translatedText = (string)$xmlData;
                return $this->_translatedText;
            }
        } else {
            return false;
        }

    }
    
    
    /**
     * Breaks a piece of text into sentences and returns an array containing the length of each sentence.
     * @param string $text text of language to break
     * @param string $lang language of the text
     * @return array
     */
    public function breakSentences($text, $lang)
    {
        if($text == '' || $lang == '') {
            return false;
        }
        
        $this->_text = $text;

        $query = "v2/Http.svc/BreakSentences?appId=" . $this->_appId . "&text=" . urlencode($this->_text) . "&language=" . $lang;
        
        if($this->_text != '') {
            $contents = $this->_remoteQuery($query);
            $xmlData = $this->_parse_xml($contents);

            if($xmlData->body->h1 == "Argument Exception") {  
                $this->_reset();
                return false;
            } else { 
                $array_length = array();
                foreach($xmlData as $length) {
                    $array_length[] = (int)$length;
                }
                
                return $array_length;
            }
        } else {
            return false;
        }

    }
    
    
    /**
     * Retrieves friendly names for the languages passed in as the parameter languageCodes, and localized using the passed locale language.
     * @param string $locale A string representing a combination of an ISO 639 two-letter lowercase culture code associated with a language and an ISO 3166 two-letter uppercase subculture code to localize the language names or a ISO 639 lowercase culture code by itself. 
     * @return string a string array containing languages names supported by the Translator Service, localized into the requested language.
     */
    public function LanguageNames($locale)
    {
        $query = "v1/Http.svc/GetLanguageNames?appId=" . $this->_appId . "&locale=" . $locale;
        $contents = $this->_remoteQuery($query);
        return $contents;
    }
    
    /**
     * Obtain a list of language codes representing languages that are supported by the Translation Service. 
     * @return array
     */
    public function LanguagesSupported()
    {
        $query = "v2/Http.svc/GetLanguagesForTranslate?appId=" . $this->_appId;
        $contents = $this->_remoteQuery($query);
        $xmlData = $this->_parse_xml($contents);
        return $xmlData;
    }
    
    /**
     * Returns a stream of a wave-file speaking the passed-in text in the desired language. 
     * @param string $text text of language to break
     * @param string $lang language of the text
     * @param string $filename name of a file where the translation will be saved,
                     by default the audio will be streamed to the browser.
     * @return array
     */
    public function Speak($text, $lang, $filename = "")
    {
        $query = "v2/Http.svc/Speak?appId=" . $this->_appId . "&text=" . urlencode($text) . "&language=" . $lang;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->_bingTranslateBaseUrl . $query);
        //curl_setopt($ch, CURLOPT_REFERER, $this->_siteUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 215);
        
        if($filename == "") {
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        } else {
            $fp = fopen($filename, "w");
            curl_setopt($ch, CURLOPT_FILE, $fp);
        }
        
        $response = curl_exec($ch); 
        return $response;
    }
    
    
}


?>