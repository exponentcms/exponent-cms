<?php
class ups {

	var $License;
	var $User;
	var $Pass;
	var $templatePath;
	var $debugMode;
	var $accessRequest;

    /**********************************************
     * $License = XML Access Code provided by UPS
     * $User = UPS.com Username
     * $Password = UPS.com Password
     * $templatePath = Path to XML templates 
     *
     **********************************************/

	function ups($license,$user,$pass){
		$this->License = $license;
		$this->User = $user;
		$this->Pass = $pass;
		$this->setTestingMode(1); 
		$this->accessRequest = false;
		$this->templatePath = 'xml/'; // No beginning slash if path is relative
	}

	function access(){
		// This will create the AccessRequest XML that belongs at the beginning of EVERY request made to UPS
		$accessXML = $this->sandwich($this->templatePath.'AccessRequest.xml', array('{LICENSE}','{USER_ID}','{PASSWORD}'), array($this->License,$this->User,$this->Pass));
		$this->accessRequest = true;
		return $accessXML;

		/*
		$accessXML = new xmlWriter();
		$accessXML->push('AccessRequest',array('xml:lang' => 'en-US'));
		$accessXML->element('AccessLicenseNumber', $this->License);
		$accessXML->element('UserId', $this->User);
		$accessXML->element('Password', $this->Pass);
		$accessXML->pop();

		$this->accessRequest = true;
		return $accessXML->getXml(); */



	}

	function request($type, $xml){
		// This function will return all of the relevant response info in the form of an Array
		if ($this->accessRequest != true) {
			die('access function has not been set');		
		} else {	
			$output = preg_replace('/[\s+]{2,}/', '', $xml);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->upsUrl.'/ups.app/xml/'.$type);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $output);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$curlReturned = curl_exec($ch);
			curl_close($ch);



			// Find out if the UPS service is down
			preg_match_all('/HTTP\/1\.\d\s(\d+)/',$curlReturned,$matches);
			foreach($matches[1] as $key=>$value) {
				if ($value != 100 && $value != 200) {
					$this->throwError("The UPS service seems to be down with HTTP/1.1 $value");
					return false;
            	} else {
					$response = strstr($curlReturned, '<?'); // Seperate the html header and the actual XML because we turned CURLOPT_HEADER to 1
					return $response;
				}
			}
		}
	}	
	
	function sandwich($templateFile, $findArray, $replaceArray){
		// This will look in the template folder for an xml template and subsitute one array for another	
		$handle=fopen($templateFile, "r");
		if($handle){
				$buffer = fread($handle, filesize($templateFile));fclose($handle);
		}
		$x=0;while($x < count($findArray)){
				$buffer = str_replace($findArray[$x],$replaceArray[$x],$buffer);++$x; 
		}
		return $buffer;
	}

	function getAvailableLayout($templateFile){
		// This function needs commented
		$handle=fopen($templateFile, "r");
		if($handle){
			$buffer = fread($handle, filesize($templateFile));fclose($handle);
		}
		preg_match_all("/(\{.*?\})/",$buffer,$availArr);
		$lines = file($templateFile);
		$items = explode(' ',$lines[1]);
		$prefix = str_replace(array('<','>',"\n","\r"),'',$items[0]);
		$x=0;
		$finalArr = array();
		$textArrayLayout .= "$".$prefix." = array();<br>";
		while($x<count($availArr[0])){
			if(!in_array($availArr[0][$x], $finalArr)){
				$finalArr[] = $availArr[0][$x];
				$key = $availArr[0][$x];
				$textArrayLayout .= "$".$prefix."['".$key."'] = '';<br>";
			}
		++$x; 
		}
	return $textArrayLayout;
	}
	
	function setTemplatePath($path){
		// TODO: set the default path to ../xml/ incase user doesn't set it
		// Set the template path for xml templates
		if($path !== ''){
			$this->templatePath = $path;
		}
		return true;
	}
	
	function setTestingMode($bool){
		if($bool == 1){
			$this->debugMode = true;
			$this->upsUrl = 'https://wwwcie.ups.com'; // Don't put a trailing slash here or world will collide.
		}else{
			$this->debugMode = false;
//			$this->upsUrl = 'https://www.ups.com';
            $this->upsUrl = 'https://onlinetools.ups.com';
		}
		return true;
	}

	function throwError($error) {
		if($this->debugMode) {
			die($error);
		}else{
			return $error;		
		}
	}
}

// Simon Willison, 16th April 2003
// Based on Lars Marius Garshol's Python XMLWriter class
// See http://www.xml.com/pub/a/2003/04/09/py-xml.html

/* class xmlWriter {
    var $xml;
    var $indent;
    var $stack = array();
    function XmlWriter($indent = '  ') {
        $this->indent = $indent;
        $this->xml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
    }
    function _indent() {
        for ($i = 0, $j = count($this->stack); $i < $j; $i++) {
            $this->xml .= $this->indent;
        }
    }
    function push($element, $attributes = array()) {
        $this->_indent();
        $this->xml .= '<'.$element;
        foreach ($attributes as $key => $value) {
            $this->xml .= ' '.$key.'="'.htmlentities($value).'"';
        }
        $this->xml .= ">\n";
        $this->stack[] = $element;
    }
    function element($element, $content, $attributes = array()) {
        $this->_indent();
        $this->xml .= '<'.$element;
        foreach ($attributes as $key => $value) {
            $this->xml .= ' '.$key.'="'.htmlentities($value).'"';
        }
        $this->xml .= '>'.htmlentities($content).'</'.$element.'>'."\n";
    }
    function emptyelement($element, $attributes = array()) {
        $this->_indent();
        $this->xml .= '<'.$element;
        foreach ($attributes as $key => $value) {
            $this->xml .= ' '.$key.'="'.htmlentities($value).'"';
        }
        $this->xml .= " />\n";
    }
    function pop() {
        $element = array_pop($this->stack);
        $this->_indent();
        $this->xml .= "</$element>\n";
    }
    function getXml() {
        return $this->xml;
    }
}
*/

class xml2Array {

	var $arrOutput = array();
	var $resParser;
	var $strXmlData;
	
	function parse($strInputXML){
		$this->resParser = xml_parser_create();
		xml_set_object($this->resParser,$this);
		xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");
		xml_set_character_data_handler($this->resParser, "tagData");
		$this->strXmlData = xml_parse($this->resParser,$strInputXML);
		if(!$this->strXmlData){
			die(sprintf("XML error: %s at line %d",
			xml_error_string(xml_get_error_code($this->resParser)),
			xml_get_current_line_number($this->resParser)));
		}
		xml_parser_free($this->resParser);
	return $this->arrOutput;
	}
	
	function tagOpen($parser, $name, $attrs){
		$tag=array("name"=>$name,"attrs"=>$attrs);
		array_push($this->arrOutput,$tag);
	}
	
	function tagData($parser, $tagData){
		if(trim($tagData)){
			if(isset($this->arrOutput[count($this->arrOutput)-1]['tagData'])){
				$this->arrOutput[count($this->arrOutput)-1]['tagData'] .= $tagData;
			}else{
				$this->arrOutput[count($this->arrOutput)-1]['tagData'] = $tagData;
			}
		}
	}
	
	function tagClosed($parser, $name){
		$this->arrOutput[count($this->arrOutput)-2]['children'][] = $this->arrOutput[count($this->arrOutput)-1];
		array_pop($this->arrOutput);
	}

}


// XML to Array
class upsxmlParser {

	var $params = array(); //Stores the object representation of XML data
    var $root = NULL;
    var $global_index = -1;
    var $fold = false;

	/* Constructor for the class
    * Takes in XML data as input( do not include the <xml> tag
    */
    function xmlparser($input, $xmlParams=array(XML_OPTION_CASE_FOLDING => 0)) {
		$xmlp = xml_parser_create();
			foreach($xmlParams as $opt => $optVal) {
        		switch( $opt ) {
          		case XML_OPTION_CASE_FOLDING:
            		$this->fold = $optVal;
           		break;
          		default:
           		break;
        		}
        		xml_parser_set_option($xmlp, $opt, $optVal);
      	}
      
		if(xml_parse_into_struct($xmlp, $input, $vals, $index)) {
        	$this->root = $this->_foldCase($vals[0]['tag']);
        	$this->params = $this->xml2ary($vals);
		}
      	xml_parser_free($xmlp);
    }
    
    function _foldCase($arg) {
		return( $this->fold ? strtoupper($arg) : $arg);
    }

	/*
	 * Credits for the structure of this function
	 * http://mysrc.blogspot.com/2007/02/php-xml-to-array-and-backwards.html
	 * 
	 * Adapted by Ropu - 05/23/2007 
	 * 
	 */

    function xml2ary($vals) {

		$mnary=array();
		$ary=&$mnary;
		foreach ($vals as $r) {
			$t=$r['tag'];
			if ($r['type']=='open') {
				if (isset($ary[$t]) && !empty($ary[$t])) {
					if (isset($ary[$t][0])){
						$ary[$t][]=array(); 
					} else {
						$ary[$t]=array($ary[$t], array());
					} 
					$cv=&$ary[$t][count($ary[$t])-1];
				} else {
					$cv=&$ary[$t];
                }
				$cv=array();
				if (isset($r['attributes'])) { 
					foreach ($r['attributes'] as $k=>$v) {
					$cv[$k]=$v;
					}
				}
                
				$cv['_p']=&$ary;
				$ary=&$cv;

				} else if ($r['type']=='complete') {
                	if (isset($ary[$t]) && !empty($ary[$t])) { // same as open
						if (isset($ary[$t][0])) {
							$ary[$t][]=array();
						} else {
							$ary[$t]=array($ary[$t], array());
						} 
					$cv=&$ary[$t][count($ary[$t])-1];
                } else {
					$cv=&$ary[$t];
				} 
				if (isset($r['attributes'])) {
					foreach ($r['attributes'] as $k=>$v) {
						$cv[$k]=$v;
					}
				}
				$cv['VALUE'] = (isset($r['value']) ? $r['value'] : '');
    
				} elseif ($r['type']=='close') {
					$ary=&$ary['_p'];
				}
        }    
        
		$this->_del_p($mnary);
		return $mnary;
	}
    
    // _Internal: Remove recursion in result array
    function _del_p(&$ary) {
        foreach ($ary as $k=>$v) {
            if ($k==='_p') {
              unset($ary[$k]);
            }
            else if(is_array($ary[$k])) {
              $this->_del_p($ary[$k]);
            }
        }
    }

    /* Returns the root of the XML data */
    function GetRoot() {
      return $this->root; 
    }

    /* Returns the array representing the XML data */
    function GetData() {
      return $this->params; 
    }
  }
?>
