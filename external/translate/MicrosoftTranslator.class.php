<?php

/**
 * MicrosoftTranslator - A PHP Wrapper for Microsoft JSON Translator API
 * 
 * @category    Translation 
 * @author      Renjith Pillai
 * @link        http://www.renjith.co.in
 * @copyright   2012 Renjith Pillai
 * @version     1.0.0
 */

class MicrosoftTranslator
{

/**
 * Some Constants
 *
 */

const SUCCESS = 'SUCCESS';

const ERROR = 'ERROR';

const UNEXPECTED_ERROR = UNEXPECTED_ERROR;

const MISSING_ERROR = MISSING_ERROR;

const TRANSLATE = 'Translate';

const GET_LANG = 'GetLanguagesForTranslation';

const ENABLE_CACHE = ENABLE_CACHE;

const CACHE_DIRECTORY = CACHE_DIRECTORY;

const LANG_CACHE_FILE = LANG_CACHE_FILE;
/**
 * Service root URL for translattion. You can get it from Nicrosoft Azure Dataset PAge
 *
 * @var unknown_type
 */
//private $serviceRootURL = 'https://api.datamarket.azure.com/Data.ashx/Bing/MicrosoftTranslator/v1/';
private $serviceRootURL = 'https://api.datamarket.azure.com/Bing/MicrosoftTranslator/v1/';
/**
 * This is the unique key you have to obtain from Microsoft
 *
 * @var string $accountKey
 */
private $accountKey = '';
/**
 * Context from account key
 *
 * @var string $context
 */
private $context = '';
/**
 * Request Invoked
 *
 * @var string $requestInvoked
 */
private $requestInvoked = '';
/**
 * From which language
 *
 * @var strng $from
 */

private $from = '';
/**
 * To which language
 *
 * @var string $to
 */
private $to = '';
/**
 * Format Raw/Atom
 *
 * @var string $to
 */
private $format = '';

/**
 * $top
 *
 * @var string $top
 */
private $top = '100';

/**
 * Translated text output
 *
 * @var string $translatedText
 */
private $translatedText = '';

/**
 * Text to Translate
 *
 * @var string $textToTranslate
 */
private $textToTranslate = '';

/**
 * Text to translate.
 *
 * @var Object $response
 */
public $response = '';


/**
 * Constructor
 *
 * @param unknown_type $accountKey
 */
public function __construct($accountKey)
{
    $this->accountKey = $accountKey;
    $this->context = $this->getContext();
}

/**
 * Translator Method which wraps all other operations
 * @param string $text
 * @param string $from
 * @param string $to
 */
public function translate($text, $from, $to, $format = 'Raw' )
{
    if(empty($to) || empty($text)) {
        $this->getErrorResponse($response, self::MISSING_ERROR, $missing);
        return;
    }
    $this->from = (!empty($this->from )) ? $this->sanitize($from) : '';
    $this->to = $this->sanitize($to);
    $this->textToTranslate = $this->sanitize($text);
    $this->format = $format;
    $request = $this->getRequest(self::TRANSLATE );

    $response = file_get_contents( $request, 0, $this->context );
    
    if(!empty($response) && isset($response)){
        $this->getSuccessResponse($response);
    } else {
        $this->getErrorResponse($response, self::UNEXPECTED_ERROR, $missing );
    }

}
/**
 * Get Languages for translation
 *
 */
public function getLanguagesSelectBox($selectBox){
    //some how Raw format gives a single string of all countries and user changing format doesnt make sense here as output is html
    $this->format = 'json';    
    $request = $this->getRequest( self::GET_LANG); 
    if( ! $response = $this->getCache( self::LANG_CACHE_FILE )) {
        $response = file_get_contents( $request, 0, $this->context );
        $this->putToCache( self::LANG_CACHE_FILE, $response );
    }
    $objResponse = json_decode($response);
    
    if(!empty($objResponse) && isset($objResponse)){
        $this->getSuccessResponse($objResponse, $selectBox);
    } else {
        $this->getErrorResponse($objResponse, self::UNEXPECTED_ERROR, $missing );
    }
    
}
/**
 * Encodes request in desirable format for Microsoft translator
 * @todo do more sanitization
 * @param string $text
 * @return unknown
 */
private function sanitize($text){
    $text = urlencode("'".$text."'");
    return $text;
}
/**
 * Success Response Object
 *
 * @param unknown_type $response
 */
private function getSuccessResponse($response, $selectBox = ''){
    $this->response = new stdClass();
    $this->response->status = self::SUCCESS;
    if($this->requestInvoked == self::TRANSLATE ) {
        $this->response->translation = $response;
        // Fot instance if you need both Raw and Json format
        if($this->format == 'Raw') {
            $this->response->jsonResponse = !function_exists('json_decode') ? $this->response : json_encode($this->response); 
        } 
    }  elseif($this->requestInvoked == self::GET_LANG ) {
        //currently it directly give selctbox
        $this->response->languageSelectBox = $this->getSelectBox($response,$selectBox);
        
    }
}

private function getSelectBox($response,$selectBox) {
    
    $options = '';
    foreach($response->d->results as $values ) {

        if(isset( $values->Code )) {
            $options.= "<option value='".$values->Code."'>".$values->Code."</option>";
            
        }
    }

    $select = "<select id ='".$selectBox['id']."' name='".$selectBox['name']. "'class='".$selectBox['class']."'>";
    $select.= $options;
    $select.= "</select>";
    
    return $select;
}
/**
 * Default error response, Currenlty i am not able to catch error for some reason, so giving custom errors
 *
 * @param unknown_type $response
 * @param unknown_type $reason
 * @param unknown_type $param
 */
private function getErrorResponse($response=null, $reason , $param=""){
    
    $this->response = new stdClass();    
    $this->response->status = self::ERROR ;
    $this->response->errorReason = str_replace("%s", $param, $reason); 
    $this->response->jsonResponse = !function_exists('json_decode') ? $this->response : json_encode($this->response);        


}
/**
 * Ger Request in Desirable format for Microsoft Translator
 *
 * @return unknown
 */
private function getRequest($type)
{
    $this->requestInvoked = $type;
    $text = (!empty($this->textToTranslate)) ? 'Text='.$this->textToTranslate : '';
    $to = (!empty($this->to)) ? $to = '&To='. $this->to : '';
    $from = (!empty($this->from)) ? '&From='. $this->from : '';
    $format = '$format='.$this->format;
    $top = '$top='.$this->top;
    $params = $text . $from . $to .'&'. $format .'&'. $top ;
    if($type == self::TRANSLATE ) {
        $request = $this->serviceRootURL. $type.'?'. $params;
    } elseif ($type == self::GET_LANG ){
        $request = $this->serviceRootURL. $type.'?'. $top.'&'. $format;
    }    
    
    return $request ;
}
/**
 * Authentication of Application key
 *
 * @return unknown
 */
private function getContext()
{
    $context = stream_context_create(array(
        'http' => array(
            'request_fulluri' => true,
            'header'  => "Authorization: Basic " . base64_encode($this->accountKey . ":" . $this->accountKey)
        )
    )); 
    
    return $context;
}


private function putToCache($file, $toCache) {
    if(self::ENABLE_CACHE  == true) {
        try {
            if(is_dir(self::CACHE_DIRECTORY)) {
                $handle = fopen(self::CACHE_DIRECTORY . $file, "w");
                fwrite($handle, $toCache);
                fclose($handle);
            }
            
        } catch (Exception $e) {
             die ('put to cache failed ' . $e->getMessage());
        }
    }
}

private function getCache($file) {
    if(self::ENABLE_CACHE  == true) {
        if(is_dir(self::CACHE_DIRECTORY) && file_exists(self::CACHE_DIRECTORY . $file)) {
            $handle = fopen(self::CACHE_DIRECTORY . $file, "r");
            $contents = '';
            
            while (!feof($handle)) 
            {
                $contents .= fread($handle, 8192);
            }
            
            fclose($handle);
            
            return $contents;
        } else {
            return false;
        }
    }
}

}