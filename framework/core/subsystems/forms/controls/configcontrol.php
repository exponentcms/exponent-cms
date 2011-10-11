<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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
/** @define "BASE" "../../../../.." */

if (!defined('EXPONENT')) exit('');

/**
 * Quantity Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class configcontrol extends formcontrol {

	public $opts  = array();
	public $title = '';

	function name() { return "Configuration Manager Control"; }
	function isSimpleControl() { return false; }
	
	function __construct($title="Config Manager", $welcome="", $opts=array()) {
		$this->type = "configmanager";
		$this->title = $title;
		$this->welcome = $welcome;
		$this->opts = $opts;
	}

	function controlToHTML($name, $label) {
		$opts_template = get_template_for_action('common', 'configopts', null);
		$opts_template->assign('opts', $this->opts);
		$html = '
			<div class="yui-skin-sam">
        			<div id="demo">
					<div id="leftopts">';
						$html .= $opts_template->render();
		$html .=		'</div>
				</div>
			</div>
		';
	
		$script = "
			var cp = new configPanel(".$this->title.", 'leftopts', '".$this->welcome."', 750, 450);
		        cp.fire();
		";	
		expJavascript::pushToFoot(array(
		    "unique"=>'cfgmgr',
		    "yui2mods"=>'dragdrop,element,animation,resize,layout',
		    "yui3mods"=>null,
		    "content"=>'//comment',
		    "src"=>PATH_RELATIVE.'framework/core/assets/js/exp-layout.js'
		 ));
        return $html;
	}
	
	function form($object) {
		$form = new form();
		if (!isset($object->identifier)) {
			$object->identifier = "";
			$object->caption = "";
			$object->default = "";
			$object->rows = 20;
			$object->cols = 60;
			$object->maxchars = 0;
		} 
		$form->register("identifier",gt('Identifier'),new textcontrol($object->identifier));
		$form->register("caption",gt('Caption'), new textcontrol($object->caption));
		$form->register("default",gt('Default'),  new texteditorcontrol($object->default));
		$form->register("rows",gt('Rows'), new textcontrol($object->rows,4,false,3,"integer"));
		$form->register("cols",gt('Columns'), new textcontrol($object->cols,4, false,3,"integer"));
		$form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel')));
		return $form;
	}
	
	function update($values, $object) {
		if ($object == null) $object = new texteditorcontrol();
		if ($values['identifier'] == "") {
			$post = $_POST;
			$post['_formError'] = gt('Identifier is required.');
			expSession::set("last_POST",$post);
			return null;
		}
		$object->identifier = $values['identifier'];
		$object->caption = $values['caption'];
		$object->default = $values['default'];
		$object->rows = intval($values['rows']);
		$object->cols = intval($values['cols']);
		$object->maxchars = intval($values['maxchars']);
		$object->required = isset($values['required']);
		
		return $object;
	
	}
	
	static function parseData($original_name,$formvalues,$for_db = false) {
		return str_replace(array("\r\n","\n","\r"),'<br />', htmlspecialchars($formvalues[$original_name])); 
	}
	
}

?>
