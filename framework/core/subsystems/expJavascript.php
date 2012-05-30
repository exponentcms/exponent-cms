<?php
##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# GPL: http://www.gnu.org/licenses/gpl.txt
#
##################################################

/**
 * This is the class expJavascript
 *
 * @package Subsystems
 * @subpackage Subsystems
 */
/** @define "BASE" "../../.." */

class expJavascript {
	public static function inAjaxAction() {
		return empty($_REQUEST['ajax_action']) ? false : true;
	}

	public static function requiresJSON() {
		return !empty($_REQUEST['json']) ? true : false;
	}
	
	public static function parseJSFiles() {
        global $userjsfiles,$expJS,$yui2js,$yui3js;
        
        ob_start();
  		include(BASE.'exponent.js.php');
        $exponent_js = ob_get_clean();
        if (MINIFY==1&&MINIFY_INLINE_JS==1) {
            include_once(BASE.'external/minify/min/lib/JSMin.php');
            $exponent_js = JSMin::minify($exponent_js);
        }
        $scripts = '<script type="text/javascript" charset="utf-8">//<![CDATA['."\r\n".$exponent_js."\r\n".'//]]></script>';

        if (MINIFY==1&&MINIFY_LINKED_JS==1) {
            // if we're minifying, we'll break our URLs apart at MINIFY_URL_LENGTH characters to allow it through
            // browser string limits
            $strlen = (ini_get("suhosin.get.max_value_length")==0) ? MINIFY_URL_LENGTH : ini_get("suhosin.get.max_value_length");
            $i = 0;
            $srt = array();
//            $srt[$i] = PATH_RELATIVE.'exponent.js.php,'.YUI3_PATH.'yui/yui-min.js,';
//            $scripts .= "\t".'<script type="text/javascript" src="'.PATH_RELATIVE.'exponent.js.php"></script>'."\r\n";
            $srt[$i] = YUI3_PATH.'yui/yui-min.js,';
            foreach ($expJS as $file) {
                if (strlen($srt[$i])+strlen($file['fullpath'])<= $strlen) {
                    $srt[$i] .= $file['fullpath'].",";
                } else {
                    $i++;
                    $srt[$i] = "";
                    $srt[$i] .= $file['fullpath'].",";
                }
            }
            foreach ($srt as $link) {
                $link = rtrim($link,",");
                $scripts .= "\t".'<script type="text/javascript" src="'.PATH_RELATIVE.'external/minify/min/index.php?f='.$link.'"></script>'."\r\n";
            }
        } else {
            $scripts .= "\t"."<!-- EXPONENT namespace setup -->"."\r\n";
//            $scripts .= "\t".'<script type="text/javascript" src="'.PATH_RELATIVE.'exponent.js.php"></script>'."\r\n";

            $scripts .= (!empty($yui3js)) ? "\t"."<!-- YUI3 Scripts -->"."\r\n\t".'<script type="text/javascript" src="'.YUI3_PATH.'yui/yui-min.js"></script>'."\r\n" : "";
            //$scripts .= "\r\n\t"."<meta id=\"yui3marker\" />"."\r\n";
            if (!empty($expJS)) {
                foreach ($expJS as $key=>$mod) {
                    //eDebug($mod['name']);
                    $scripts .= "\t".'<script type="text/javascript" src="'.$mod['fullpath'].'"></script>'."\r\n";
                }
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
        if (MINIFY==1&&MINIFY_INLINE_JS==1) {
            include_once(BASE.'external/minify/min/lib/JSMin.php');
            $html = JSMin::minify($html);
        }
        return '<script type="text/javascript" charset="utf-8">//<![CDATA['."\r\n".$html."\r\n".'//]]></script>';
	}
	
    public static function pushToFoot($params) {
    	global $js2foot,$yui2js,$yui3js,$expJS;

    	if (self::inAjaxAction()) {
		    echo "<div class=\"io-execute-response\">";
		    
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

	/** exdoc
	 * Takes a stdClass object from PHP, and generates the
	 * corresponding Javascript class function.  The data in the
	 * members of the PHP object is not important, only the
	 * presence and names of said members.  Returns the
	 * javascript class function code.
	 *
	 * @param Object $object The object to translate
	 * @param string $name What to call the class in javascript
     * @return string
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

	/** exdoc
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
     * @return string
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

    public static function panel($params) {
        $content = "<div class=\"pnlmsg\">".htmlentities($params['content'])."</div>";
        $id = "exppanel".$params['id'];
        $width  = !empty($params['width']) ? $params['width'] : "300px";
        $type  = !empty($params['type']) ? $params['type'] : "info";
        $dialog  = !empty($params['dialog']) ? explode(":",$params['dialog']) : "";
        $header  = !empty($params['header']) ? $params['header'] : "&nbsp;";
        $renderto  = !empty($params['renderto']) ? $params['renderto'] : 'document.body';
        $on  = !empty($params['on']) ? $params['on'] : 'load';
        $onnogo  = !empty($params['onnogo']) ? $params['onnogo'] : '';
        $onyesgo  = !empty($params['onyesgo']) ? $params['onyesgo'] : '';
        $trigger  = !empty($params['trigger']) ? '"'.$params['trigger'].'"' : 'selfpop';
        $zindex  = !empty($params['zindex']) ? $params['zindex'] : "50";
        //$hide  = !empty($params['hide']) ? $params['hide'] : "hide";
        $fixedcenter  = !empty($params['fixedcenter']) ? $params['fixedcenter'] : "true";
        $fade  = !empty($params['fade']) ? $params['fade'] : null;
        $modal  = !empty($params['modal']) ? $params['modal'] : "true";
        $draggable  = empty($params['draggable']) ? "false" : $params['draggable'];
        $constraintoviewport  = !empty($params['constraintoviewport']) ? $params['constraintoviewport'] : "true";
        $fade  = !empty($params['fade']) ? "effect:{effect:YAHOO.widget.ContainerEffect.FADE,duration:".$params['fade']."}," : "";
        $close  = !empty($params['close']) ? $params['close'] : "true";

        $script = "";
        if (is_array($dialog)) {
            $script .= "
                var handleYes = function(e,o) {
                    this.hide();";
                    if ($onyesgo!="") {
                        $script .= "document.location = '".trim($onyesgo)."'";
                    };
            $script .= "};
                var handleNo = function(e,o) {
                    this.hide();";
                    if ($onyesgo!="") {
                        $script .= "var textlink = '".trim($onnogo)."';";
                        $script .= 'document.location = textlink.replace(/&amp;/g,"&");';
                    };
            $script .= "};";

            $script .= "var ".$id." = new YAHOO.widget.SimpleDialog('".$id."', { ";
            $script .= "buttons: [ { text:'".$dialog[0]."', handler:handleYes, isDefault:true },{ text:'".$dialog[1]."',  handler:handleNo } ],";
            //$script .= "text: 'Do you want to continue?',";
        } else {
            $script .= "var ".$id." = new YAHOO.widget.Panel('".$id."', { ";
        }

        $script .= "fixedcenter:".$fixedcenter.",
                draggable:".$draggable.",
                modal:".$modal.",
                class:'exp-".$type." ".$hide."',
                zIndex:".$zindex.","
                .$fade.
                "width:'".$width."',
                visible:false,
                constraintoviewport:".$constraintoviewport.",
                close:".$close." } );";

            $script .= $id.".setHeader('".$header."');";
            $script .= "var pnlcontent = ".$content.";";

                $script .= $id.".setBody('<span class=\"type-icon\"></span>'+pnlcontent);";

            $script .= $id.".setFooter('".$footer."</div>');";
            $script .= $id.".render(".$renderto.");";
            $script .= "YAHOO.util.Dom.addClass('".$id."','exp-".$type."');";
            if ($hide==false) {
                $script .= "YAHOO.util.Dom.addClass('".$id."','".$hide."');";
            }

        switch ($trigger) {
            case 'selfpop':
            $script .= "YAHOO.util.Event.onDOMReady(".$id.".show, ".$id.", true);";
                break;

            default:
            $script .= "YAHOO.util.Event.on(".$trigger.", '".$on."', function(e,o){
                YAHOO.util.Event.stopEvent(e);
                o.show();
            }, ".$id.", true);";
            break;
        }

        expJavascript::pushToFoot(array(
            "unique"=>'pop-'.$params['name'],
            "yui2mods"=>'animation,container',
            "yui3mods"=>null,
            "content"=>$script,
            "src"=>""
         ));
    }

}

?>