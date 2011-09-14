<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the expJavascript class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Adam Kessler <adam@oicgroup.net>
 * @version 2.0.0
 */
/** @define "BASE" "../../.." */


/**
 * This is the class expJavascript
 *
 * @subpackage Core-Subsystems
 * @package Framework
 */

class expJavascript {
	public static function inAjaxAction() {
		return empty($_REQUEST['ajax_action']) ? false : true;
	}

	public static function requiresJSON() {
		return !empty($_REQUEST['json']) ? true : false;
	}
	
	public static function parseJSFiles() {
        global $userjsfiles,$expJS,$yui2js,$yui3js;
        
    	$scripts = "";
        if (!empty($yui2js)) {
        	require_once(BASE.'external/lissa/class.lissa.php');        
        
            // instantiate loader class for yui2
            $yui2Loader = new Lissa(YUI2_VERSION, null);

            // instantiate loader class for yui3
            //$yui3Loader = new Lissa(YUI3_VERSION, null, $expJS);

            // load Exponent's yui2 dependencies
            $yui2Loader->load("dom");
            $yui2Loader->load("event");

            // load yui2 modules called for via the scipt plugin
            foreach ($yui2js as $key=>$mod) {
                $yui2Loader->load($mod);
            }
            $yui2Loader->combine = intval(MINIFY);
            $scripts = "\r\n\t"."<!-- YUI2 Scripts -->"."\r\n";
            $scripts .= $yui2Loader->scripts()."\r\n";
        }
        
        // load yui3 modules called for via the scipt plugin
        // if (!empty($yui3js)) {
        //     foreach ($yui3js as $key=>$mod) {
        //         $yui3Loader->load($mod);
        //     }
        // }
        
        // load external (non-yui) scripts
        // if (!empty($expJS)) {
        //     foreach ($expJS as $key=>$mod) {
        //         $yui3Loader->load($mod['name']);
        //     }
        // }
                
        // $yui3Loader->combine = intval(MINIFY);
        
        $scripts .= "\t"."<!-- EXPONENT namespace setup -->"."\r\n";
        $scripts .= "\t".'<script type="text/javascript" src="'.PATH_RELATIVE.'exponent.js.php"></script>'."\r\n";

        $scripts .= (!empty($yui3js)) ? "\r\n\t"."<!-- YUI3 Scripts -->"."\r\n\t".'<script type="text/javascript" src="'.YUI3_PATH.'yui/yui-min.js"></script>'."\r\n" : "";
        //$scripts .= "\r\n\t"."<meta id=\"yui3marker\" />"."\r\n";
        if (!empty($expJS)) {
            foreach ($expJS as $key=>$mod) {
                //eDebug($mod['name']);
                $scripts .= "\t".'<script type="text/javascript" src="'.$mod['fullpath'].'"></script>'."\r\n";
            }
        }



        //$html .= "\t".$expYUIJSLoader->js()."\r\n";
        return $scripts;
	}
	
	public static function footJavascriptOutput() {
        global $js2foot;
        $html = "";
        // need to have some control over which scripts execute first.
        // solution: alphabetical by unique
        if(!empty($js2foot)){
            ksort($js2foot);
            foreach($js2foot as $key=>$file){
                $html.= $file."\r\n";
            }            
        } 
        
        return '<script type="text/javascript" charset="utf-8">//<![CDATA['."\r\n".$html.'//]]></script>';
        
	}
	
    public static function pushToFoot($params) {
    	global $js2foot,$yui2js,$yui3js,$expJS;

    	if (self::inAjaxAction()) {
		    echo "<div class=\"io-execute-response\">";
		    
            if(!empty($params['yui2mods'])){
                $toreplace = array('"',"'"," ");
                $stripmodquotes = str_replace($toreplace, "", $params['yui2mods']);               
                $splitmods = explode(",",$stripmodquotes);

                 require_once(BASE.'external/lissa/class.lissa.php');        

                // instantiate loader class for yui2
                $yui2Loader = new Lissa(YUI2_VERSION, null);

                // instantiate loader class for yui3
                //$yui3Loader = new Lissa(YUI3_VERSION, null, $expJS);

                // load yui2 modules called for via the scipt plugin
                foreach ($splitmods as $key=>$mod) {
                    $yui2Loader->load($mod);
                }
                $yui2Loader->combine = intval(MINIFY);
                $scripts = "\r\n\t"."<!-- YUI2 Scripts -->"."\r\n";
                $scripts .= $yui2Loader->scripts()."\r\n";
                echo $scripts;
            }
            

    	    if ($params['src']) {
                echo '<script type="text/javascript" src="'.$params['src'].'"></script>';
    	    }
    	    
		    echo "
		    <script id=\"".$params['unique']."\" type=\"text/javascript\" charset=\"utf-8\">
		      ".$params['content']."
		    </script>
		    </div>
		    ";
		    return true;
    	}


    	if (!empty($params['src'])) {
    	    //$src = str_replace(URL_FULL,PATH_RELATIVE,$params['src']);
    	    $src = $params['src'];
    	    //if (file_exists(str_replace(PATH_RELATIVE,"",$src))) {
                $expJS[$params['unique']] = array(
					"name" => $params['unique'],
					"type" => 'js',
					"fullpath" => $src
                );
            // } else {
            //     flash('error',"Exponent could not find ".$src.". Check to make sure the path is correct.");
            // }
    	}

        // if(!empty($params['yui2mods'])){
        //             $toreplace = array('"',"'"," ");
        //             $stripmodquotes = str_replace($toreplace, "", $params['yui2mods']);               
        //             $splitmods = explode(",",$stripmodquotes);
        // 
        //             foreach ($splitmods as $key=>$val){
        //                 $yui2js[$val] = $val;
        //             }
        //         }

		if (isset($params['content']) && stristr($params['content'],"use('*',") && isset($params['yui3mods'])) {
            $params['content'] = str_replace("use('*',",('use(\''.str_replace(',','\',\'',$params['yui3mods']).'\','),$params['content']);
            $yui3js["yui"] = "yui";
		}

    	if(!empty($params['yui3mods'])){
            $toreplace = array('"',"'"," ");
            $stripmodquotes = str_replace($toreplace, "", $params['yui3mods']);               
            $splitmods = explode(",",$stripmodquotes);

            foreach ($splitmods as $key=>$val){
                $yui3js[$val] = $val;
            }
        }

    	if (isset($params['content'])) $js2foot[$params['unique']] = $params['content'];
    }

	public static function ajaxReply($replyCode=200, $replyText='Ok', $data) {
		$ajaxObj['replyCode'] = $replyCode;
		$ajaxObj['replyText'] = $replyText;
		if (isset($data)) {
			$ajaxObj['data'] = $data;
			if (is_array($data)) {
				$ajaxObj['replyCode'] = 201;
			} elseif (is_string($data)) {
				$ajaxObj['replyCode'] = 202;
			} elseif (is_bool($data)) {
				$ajaxObj['replyCode'] = 203;
			} elseif (empty($data)) {
				$ajaxObj['replyCode'] = 204;
			}
		}
		return json_encode($ajaxObj);
	}

	/* exdoc
	 * Takes a stdClass object from PHP, and generates the
	 * corresponding Javascript class function.  The data in the
	 * members of the PHP object is not important, only the
	 * presence and names of said members.  Returns the
	 * javascript class function code.
	 *
	 * @param Object $object The object to translate
	 * @param string $name What to call the class in javascript
	 * @node Subsystems:Javascript
	 */
	public static function jClass($object, $name) {
		$otherclasses = array();
		$js = "function $name(";
		$js1 = "";
		foreach (get_object_vars($object) as $var=>$val) {
			$js .= "var_$var, ";
			$js1 .= "\tthis.var_$var = var_$var;\n";
			if (is_object($val)) {
				$otherclasses[] = array($name . "_" . $var, $val);
			}
		}
		$js = substr($js, 0, -2) . ") {\n" . $js1 . "}\n";
		foreach ($otherclasses as $other) {
			echo "/// Other Object : ".$other[1] . ", " . $other[0] ."\n";
			$js .= "\n" . self::jClass($other[1], $other[0]);
		}
		return $js;
	}

	/* exdoc
	 * Takes a stdClass object from PHP, and generates the
	 * corresponding Javascript calls to make a new Javascript
	 * object.  In order for the resulting Javascript to function
	 * properly, a call to expJavascript_class must have been
	 * made previously, and the same $name attribute used. Returns
	 * the javascript code to create a new object.
	 *
	 * The data in the members of the PHP object will be used to
	 * populate the members of the new Javascript object.
	 *
	 * @param Object $object The object to translate
	 * @param string $name The name of the javascript class
	 * @node Subsystems:Javascript
	 */
	public static function jObject($object, $name="Array") {
		$js = "new $name(";

		//PHP4: "foreach" does not work on object properties
		if (is_object($object)) {
			//transform the object into an array
			$object = get_object_vars($object);
		}

		foreach ($object as $var=>$val) {
			switch (gettype($val)){
				case "string":
					$js .= "'" . str_replace( array("'", "\r\n", "\n"),	array("&apos;", "\\r\\n", "\\n"), $val) . "'";
					break;
				case "array":
					$js .= self::jObject($val);
					break;
				case "object":
					$js .= self::jObject($val, $name . "_" . $var);
					break;
				default:
					$js .= '"' . $val . '"';
			}
			$js .= ', ';
		}

		//if there have been any values
		if($js != "new $name(") {
			//remove the useless last ", "
			$js = substr($js, 0, -2);
		}

		//close with ")"
		return  $js . ")";
	}
}
?>
