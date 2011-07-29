<?php

class expString {

	static function convertUTF($string) {
		return $string = str_replace('?', '', htmlspecialchars($string, ENT_IGNORE, 'UTF-8'));
	} 
		
	static function validUTF($string) {
		if(!mb_check_encoding($string, 'UTF-8') OR !($string === mb_convert_encoding(mb_convert_encoding($string, 'UTF-32', 'UTF-8' ), 'UTF-8', 'UTF-32'))) {
			return false;
		}		
		return true;
	}

	static function onlyReadables($string) {
		for ($i=0;$i<strlen($string);$i++) {
			$chr = $string{$i};
			$ord = ord($chr);
			if ($ord<32 or $ord>126) {
			$chr = "~";
			$string{$i} = $chr;
			}
		}
		return str_replace("~", "", $string);
	}
	
	static function parseAndTrim($str, $unescape=false) {

        $str = str_replace("<br>"," ",$str);
        $str = str_replace("</br>"," ",$str);
        $str = str_replace("<br/>"," ",$str);
        $str = str_replace("<br />"," ",$str);
        $str = str_replace('"',"&quot;",$str);
        $str = str_replace("'","&#39;",$str);
        $str = str_replace("?","&rsquo;",$str);
        $str = str_replace("?","&lsquo;",$str);
        $str = str_replace("?","&#174;",$str);
        $str = str_replace("?","-", $str);
        $str = str_replace("?","&#151;", $str); 
        $str = str_replace("?", "&rdquo;", $str);
        $str = str_replace("?", "&ldquo;", $str);
        $str = str_replace("\r\n"," ",$str); 
        $str = str_replace("?","&#188;",$str);
        $str = str_replace("?","&#189;",$str);
        $str = str_replace("?","&#190;",$str);
		$str = str_replace("?", "&trade;", $str);
		$str = trim($str);
		
        if ($unescape) {
			$str = stripcslashes($str);  
		} else {
	        $str = addslashes($str);
        }

        return $str;
    }
	
	static function convertXMLFeedSafeChar($str) {
		$str = str_replace("<br>","",$str);
        $str = str_replace("</br>","",$str);
        $str = str_replace("<br/>","",$str);
        $str = str_replace("<br />","",$str);
        $str = str_replace("&quot;",'"',$str);
        $str = str_replace("&#39;","'",$str);
        $str = str_replace("&rsquo;","'",$str);
        $str = str_replace("&lsquo;","'",$str);        
        $str = str_replace("&#174;","",$str);
        $str = str_replace("–","-", $str);
        $str = str_replace("—","-", $str); 
        $str = str_replace("”", '"', $str);
        $str = str_replace("&rdquo;",'"', $str);
        $str = str_replace("“", '"', $str);
        $str = str_replace("&ldquo;",'"', $str);
        $str = str_replace("\r\n"," ",$str); 
        $str = str_replace("¼"," 1/4",$str);
        $str = str_replace("&#188;"," 1/4", $str);
        $str = str_replace("½"," 1/2",$str);
        $str = str_replace("&#189;"," 1/2",$str);
        $str = str_replace("¾"," 3/4",$str);
        $str = str_replace("&#190;"," 3/4",$str);
        $str = str_replace("™", "(TM)", $str);
        $str = str_replace("&trade;","(TM)", $str);
        $str = str_replace("&reg;","(R)", $str);
        $str = str_replace("®","(R)",$str);        
        $str = str_replace("&","&amp;",$str);      
		$str = str_replace(">","&gt;",$str);      		
        return trim($str);
	}

}
?>