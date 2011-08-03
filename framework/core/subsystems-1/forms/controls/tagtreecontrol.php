<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

if (!defined('EXPONENT')) exit('');

/**
 * Tag Tree Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class tagtreecontrol extends formcontrol {

	var $jsHooks = array();
	
	function name() { return "Nested Node Checkbox Dragdrop Tree"; }
	function isSimpleControl() { return false; }
	function getFieldDefinition() { 
		return array();
	}

	function __construct($params) {
		global $db;
		
		if (!empty($params['values'])) {
    		foreach ($params['values'] as $key=>$var) {
    			$this->values[$key] = $var->id;
    		}
		}
		
		$this->menu = !empty($params['menu']) ? "true" : "false";
		$this->object = $params['nodes'];
		$this->addable = (bool)$params['addable'];
		$this->draggable = $params['draggable'];
		$this->checkable = $params['checkable'];
		$this->expandonstart = empty($params['expandonstart']) ? "false" : "true";
		
		// setup the controller for this..if it wasn't passed in we'll default to expTag
		$this->controller_classname = getControllerClassName(isset($params['controller']) ? $params['controller'] : 'expTag');
		$this->controller = new $this->controller_classname();
		
		// check if a model name was passed in..if not we'll guess it from the controller
		$this->modelname = isset($params['model']) ? $params['model'] : $this->controller->basemodel_name;
		$this->model = new $this->modelname();
		
		// get all the tags.
		$this->tags = $this->model->getFullTree();
		//eDebug($this->tags);
	}
	
	function toHTML($label,$name) {
		$link = exponent_core_makeLink(array("module"=>$this->controller->baseclassname,"action"=>"edit","parent"=>0));
		$html = "";
        if ($this->menu == "true") {
            if ($this->addable) $html = '<a href="' . $link . '">Add a new tag</a> | ';        
		    $html .= '<a href="#" id="expandall">Expand All</a> | ';
            $html .= '<a href="#" id="collapseall">Collapse All</a>';
		}
        
		$html .= '<div id="'.$this->id.'" class="nodetree loading">Loading Data</div>';
		
		foreach($this->tags as $i=>$val){
			if (!empty($this->values) && in_array($val->id,$this->values)) {
				$this->tags[$i]->value = true;
			} else {
				$this->tags[$i]->value = false;
			}
			$this->tags[$i]->draggable = $this->draggable; 
			$this->tags[$i]->checkable = $this->checkable; 
		}
        
		$obj = json_encode($this->tags);
		$script = '
			var obj2json = '.$obj.';
			YAHOO.util.Event.onDOMReady(function(){
				expTree.init("'.$this->id.'",obj2json,"'.$this->modelname.'",'.$this->menu.','.$this->expandonstart.');
			});
		';
//		exponent_javascript_toFoot('expddtree', 'treeview,menu,animation,dragdrop,json,container,connection', null, $script, PATH_RELATIVE.'framework/core/assets/js/exp-tree.js');
		expJavascript::pushToFoot(array(
		    "unique"=>'expddtree',
		    "yui2mods"=>'treeview,menu,animation,dragdrop,json,container,connection',
		    "yui3mods"=>null,
		    "content"=>$script,
		    "src"=>PATH_RELATIVE.'framework/core/assets/js/exp-tree.js'
		 ));
		return $html;
	}
	
	function controlToHTML($name, $label) {
	}
}

?>
