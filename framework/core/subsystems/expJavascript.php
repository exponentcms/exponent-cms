<?php
##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
        if (!empty($_REQUEST['apikey'])) {
            return true;
        }
		return empty($_REQUEST['ajax_action']) ? false : true;
	}

	public static function requiresJSON() {
        if (!empty($_REQUEST['apikey'])||!empty($_REQUEST['jsonp'])) {
            return 'jsonp';
        }
		return !empty($_REQUEST['json']) ? true : false;
	}
	
	public static function parseJSFiles() {
        global $userjsfiles,$expJS,$yui2js,$yui3js,$jqueryjs, $head_config;

        $scripts = '';
        // remove duplicate scripts since it's inefficient and crashes minify
        $newexpJS = array();
        $usedJS = array();
        foreach($expJS as $eJS) {
            if (!in_array($eJS['fullpath'],$usedJS)) {
                $usedJS[] = $eJS['fullpath'];
                $newexpJS[$eJS['name']] = $eJS;
            }
        }
        $expJS = $newexpJS;
        ob_start();
  		include(BASE.'exponent.js.php');
        $exponent_js = ob_get_clean();
        if (MINIFY==1&&MINIFY_INLINE_JS==1) {
            include_once(BASE.'external/minify/min/lib/JSMin.php');
            $exponent_js = JSMin::minify($exponent_js);
        } else {
            $scripts .= "<!-- EXPONENT namespace setup -->"."\r\n";
        }
        $scripts .= '<script type="text/javascript" charset="utf-8">//<![CDATA['."\r\n".$exponent_js."\r\n".'//]]></script>'."\r\n";

        if (MINIFY==1&&MINIFY_LINKED_JS==1) {
            // if we're minifying, we'll break our URLs apart at MINIFY_URL_LENGTH characters to allow it through
            // browser string limits
            $strlen = (ini_get("suhosin.get.max_value_length")==0) ? MINIFY_URL_LENGTH : ini_get("suhosin.get.max_value_length");
            $i = 0;
            $srt = array();
            $srt[$i] = '';
            if (!empty($yui3js)) $srt[$i] = YUI3_RELATIVE.'yui/yui-min.js,';
            if (!empty($jqueryjs) || $head_config['framework'] == 'jquery' || $head_config['framework'] == 'bootstrap') {
                if (strlen($srt[$i])+strlen(JQUERY_SCRIPT)<= $strlen && $i <= MINIFY_MAX_FILES) {
                    $srt[$i] .= JQUERY_SCRIPT.",";
                } else {
                    $i++;
//                    $srt[$i] = "";
                    $srt[$i] = JQUERY_SCRIPT.",";
                }
                if ($head_config['framework'] == 'bootstrap') {
//                    if (strlen($srt[$i])+strlen(PATH_RELATIVE.'external/bootstrap/js/bootstrap.min.js')<= $strlen && $i <= MINIFY_MAX_FILES) {
//                        $srt[$i] .= PATH_RELATIVE.'external/bootstrap/js/bootstrap.min.js'.",";
//                    } else {
//                        $i++;
////                        $srt[$i] = "";
//                        $srt[$i] = 'external/bootstrap/js/bootstrap.min.js'.",";
//                    }
                    $lessvars = array_merge(array('swatch'=>SWATCH), array('themepath'=>'"../../../themes/'.DISPLAY_THEME.'/less"'), !empty($head_config['lessvars']) ? $head_config['lessvars'] : array());
                    expCSS::pushToHead(array(
               		    "lessprimer"=>"external/bootstrap/less/bootstrap.less",
                        "lessvars"=>$lessvars,
                    ));
                    expCSS::pushToHead(array(
               		    "lessprimer"=>"external/bootstrap/less/responsive.less",
                        "lessvars"=>$lessvars,
                    ));
                }
                if (!empty($jqueryjs)) foreach ($jqueryjs as $mod) {
                    if ($mod == 'jqueryui') {
                        if (strlen($srt[$i])+strlen(JQUERYUI_SCRIPT) <= $strlen && $i <= MINIFY_MAX_FILES) {
                            $srt[$i] .= JQUERYUI_SCRIPT.",";
                        } else {
                            $i++;
//                            $srt[$i] = "";
                            $srt[$i] = JQUERYUI_SCRIPT.",";
                        }
                        expCSS::pushToHead(array(
                            'css_primer'=>JQUERYUI_CSS
                        ));
                    } else {
                        if (file_exists(BASE.'themes/'.DISPLAY_THEME.'/js/'.$mod.'.js')) {
                            if (strlen($srt[$i])+strlen(PATH_RELATIVE.'themes/'.DISPLAY_THEME.'/js/'.$mod.'.js')<= $strlen && $i <= MINIFY_MAX_FILES) {
                                $srt[$i] .= PATH_RELATIVE.'themes/'.DISPLAY_THEME.'/js/'.$mod.'.js'.",";
                            } else {
                                $i++;
//                                $srt[$i] = "";
                                $srt[$i] = PATH_RELATIVE.'themes/'.DISPLAY_THEME.'/js/'.$mod.'.js'.",";
                            }
                            if (file_exists(BASE.'themes/'.DISPLAY_THEME.'/less/'.$mod.'.less')) {
                                expCSS::pushToHead(array(
                           		    "unique"=>$mod,
                           		    "lesscss"=>PATH_RELATIVE.'themes/'.DISPLAY_THEME.'/less/'.$mod.'.less',
                           		    )
                           		);
                            } elseif (file_exists(BASE.'themes/'.DISPLAY_THEME.'/css/'.$mod.'.css')) {
                                expCSS::pushToHead(array(
                           		    "unique"=>$mod,
                           		    "link"=>PATH_RELATIVE.'themes/'.DISPLAY_THEME.'/css/'.$mod.'.css',
                           		    )
                           		);
                            }
                        } elseif (file_exists(JQUERY_PATH.'addons/js/'.$mod.'.js')) {
//                            $scripts .= "\t".'<script type="text/javascript" src="'.JQUERY_RELATIVE.'addons/js/'.$mod.'.js"></script>'."\r\n";
                            if (strlen($srt[$i])+strlen(JQUERY_RELATIVE.'addons/js/'.$mod.'.js')<= $strlen && $i <= MINIFY_MAX_FILES) {
                                $srt[$i] .= JQUERY_RELATIVE.'addons/js/'.$mod.'.js'.",";
                            } else {
                                $i++;
//                                $srt[$i] = "";
                                $srt[$i] = JQUERY_RELATIVE.'addons/js/'.$mod.'.js'.",";
                            }
                            if (file_exists(JQUERY_PATH.'addons/less/'.$mod.'.less')) {
                                expCSS::pushToHead(array(
                           		    "lessprimer"=>JQUERY_RELATIVE.'addons/less/'.$mod.'.less',
                           		    )
                           		);
                            } elseif (file_exists(JQUERY_PATH.'addons/css/'.$mod.'.css')) {
                                expCSS::pushToHead(array(
                           		    "css_primer"=>JQUERY_RELATIVE.'addons/css/'.$mod.'.css',
                           		    )
                           		);
                            }
                        }
                    }
                }
            }
            foreach ($expJS as $file) {
                if (!empty($file['fullpath']) && file_exists($_SERVER['DOCUMENT_ROOT'].$file['fullpath'])) {
                    if (strlen($srt[$i])+strlen($file['fullpath'])<= $strlen && $i <= MINIFY_MAX_FILES) {
                        $srt[$i] .= $file['fullpath'].",";
                    } else {
                        $i++;
    //                    $srt[$i] = "";
                        $srt[$i] = $file['fullpath'].",";
                    }
                }
            }
            foreach ($srt as $link) {
                $link = rtrim($link,",");
                $scripts .= "\t".'<script type="text/javascript" src="'.PATH_RELATIVE.'external/minify/min/index.php?f='.$link.'"></script>'."\r\n";
            }
        } else {
            if (!empty($jqueryjs) || $head_config['framework'] == 'jquery' || $head_config['framework'] == 'bootstrap') {
                $scripts .= "\t"."<!-- jQuery Scripts -->"."\r\n";
                $scripts .= "\t".'<script type="text/javascript" src="'.JQUERY_SCRIPT.'"></script>'."\r\n";
                if (!empty($head_config['framework']) && $head_config['framework'] == 'bootstrap') {
                    $lessvars = array_merge(array('swatch'=>SWATCH), array('themepath'=>'"../../../themes/'.DISPLAY_THEME.'/less"'), !empty($head_config['lessvars']) ? $head_config['lessvars'] : array());
                    expCSS::pushToHead(array(
               		    "lessprimer"=>"external/bootstrap/less/bootstrap.less",
                        "lessvars"=>$lessvars,
                    ));
                    expCSS::pushToHead(array(
               		    "lessprimer"=>"external/bootstrap/less/responsive.less",
                        "lessvars"=>$lessvars,
                    ));
                }
                if (!empty($jqueryjs)) foreach ($jqueryjs as $mod) {
                    if ($mod == 'jqueryui') {
                        $scripts .= "\t".'<script type="text/javascript" src="'.JQUERYUI_SCRIPT.'"></script>'."\r\n";
                        expCSS::pushToHead(array(
                            'css_primer'=>JQUERYUI_CSS
                        ));
                    } else {
                        if (file_exists(BASE.'themes/'.DISPLAY_THEME.'/js/'.$mod.'.js')) {
                            $scripts .= "\t".'<script type="text/javascript" src="'.PATH_RELATIVE.'themes/'.DISPLAY_THEME.'/js/'.$mod.'.js"></script>'."\r\n";
                            if (file_exists(BASE.'themes/'.DISPLAY_THEME.'/less/'.$mod.'.less')) {
                                expCSS::pushToHead(array(
                           		    "unique"=>$mod,
                           		    "lesscss"=>PATH_RELATIVE.'themes/'.DISPLAY_THEME.'/less/'.$mod.'.less',
                           		    )
                           		);
                            } elseif (file_exists(BASE.'themes/'.DISPLAY_THEME.'/css/'.$mod.'.css')) {
                                expCSS::pushToHead(array(
                           		    "unique"=>$mod,
                           		    "link"=>PATH_RELATIVE.'themes/'.DISPLAY_THEME.'/css/'.$mod.'.css',
                           		    )
                           		);
                            }
                        } elseif (file_exists(JQUERY_PATH.'addons/js/'.$mod.'.js')) {
                            $scripts .= "\t".'<script type="text/javascript" src="'.JQUERY_RELATIVE.'addons/js/'.$mod.'.js"></script>'."\r\n";
                            if (file_exists(JQUERY_PATH.'addons/less/'.$mod.'.less')) {
                                expCSS::pushToHead(array(
                           		    "lessprimer"=>JQUERY_RELATIVE.'addons/less/'.$mod.'.less',
                           		    )
                           		);
                            } elseif (file_exists(JQUERY_PATH.'addons/css/'.$mod.'.css')) {
                                expCSS::pushToHead(array(
                           		    "css_primer"=>JQUERY_RELATIVE.'addons/css/'.$mod.'.css',
                           		    )
                           		);
                            }
                        }
                    }
                }
            }
            $scripts .= (!empty($yui3js)) ? "\t"."<!-- YUI3 Script -->"."\r\n\t".'<script type="text/javascript" src="'.YUI3_RELATIVE.'yui/yui-min.js"></script>'."\r\n" : "";
            if (!empty($expJS)) {
                $scripts .= "\t"."<!-- Other Scripts -->"."\r\n";
                foreach ($expJS as $mod) {
                    $scripts .= "\t".'<script type="text/javascript" src="'.$mod['fullpath'].'"></script>'."\r\n";
                }
            }
        }

        return $scripts;
	}
	
	public static function footJavascriptOutput() {
        global $js2foot;

        $html = "";
        // need to have some control over which scripts execute first.
        // solution: alphabetical by unique
        if(!empty($js2foot)){
            ksort($js2foot);
            foreach($js2foot as $file){
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
    	global $js2foot,$yui2js,$yui3js,$jqueryjs,$expJS;

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

            foreach ($splitmods as $val){
                $yui3js[$val] = $val;
            }
        }

        if(!empty($params['jquery'])){
            $toreplace = array('"',"'"," ");
            $stripmodquotes = str_replace($toreplace, "", $params['jquery']);
            $splitmods = explode(",",$stripmodquotes);

            foreach ($splitmods as $val){
                $jqueryjs[$val] = $val;
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
        $header  = !empty($params['header']) ? $params['header'] : "&#160;";
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
//FIXME $hide & $footer are not defined below
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

        self::pushToFoot(array(
            "unique"=>'pop-'.$params['name'],
            "yui2mods"=>'animation,container',
//            "yui3mods"=>null,
            "content"=>$script,
            "src"=>""
         ));
    }

}

?>