<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright 2006 Maxim Mueller
# Written and Designed by James Hunt
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

/* exdoc
 * The definition of this constant lets other parts of the system know 
 * that the subsystem has been included for use.
 * @node Subsystems:Javascript
 */
define("SYS_JAVASCRIPT",1);

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
function exponent_javascript_class($object, $name) {
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
		$js .= "\n" . exponent_javascript_class($other[1], $other[0]);
	}
	return $js;
}

/* exdoc
 * Takes a stdClass object from PHP, and generates the
 * corresponding Javascript calls to make a new Javascript
 * object.  In order for the resulting Javascript to function
 * properly, a call to exponent_javascript_class must have been
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
function exponent_javascript_object($object, $name="Array") {
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
				$js .= exponent_javascript_object($val);
				break;
			case "object":
				$js .= exponent_javascript_object($val, $name . "_" . $var);
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

/* exdoc
 * Generates the Javascript code to instantiate an array
 * identical to the passed array.  Returns The javascript code
 * to create and populate the array in javascript.
 *
 * DEPRECATED: - Rationale: remove needless code duplication, in JavaScript Array==Object
 *
 * @param $array The PHP array to translate
 * @node Subsystems:Javascript
 */
function exponent_javascript_array($array) {
	return exponent_javascript_object($array);
}

function exponent_javascript_toFoot($unique,$y2mods,$y3mods,$content,$externaljssource=""){
    expJavascript::pushToFoot(array(
        "unique"=>$unique,
        "yui2mods"=>$y2mods,
        "yui3mods"=>$y3mods,
        "content"=>$content,
        "src"=>$externaljssource
     ));
}

function exponent_javascript_inAjaxAction() {
	return isset($_REQUEST['ajax_action']) ? true : false;
}

function exponent_javascript_ajaxReply($replyCode=200, $replyText='Ok', $data) {
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

function exponent_javascript_panel($params) {
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

	exponent_javascript_toFoot('pop-'.$params['name'], 'animation,container', null, $script);
}

?>
