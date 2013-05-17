<?php

##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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
 * @package    Subsystems-Forms
 * @subpackage Control
 */
class tagtreecontrol extends formcontrol {

    var $jsHooks = array();

    static function name() {
        return "Nested Node Checkbox Dragdrop Tree";
    }

    static function isSimpleControl() {
        return false;
    }

    static function getFieldDefinition() {
        return array();
    }

    function __construct($params) {
        global $db;

        if (!empty($params['values'])) {
            foreach ($params['values'] as $key=> $var) {
                $this->values[$key] = $var->id;
            }
        }

        $this->menu          = !empty($params['menu']) ? "true" : "false";
        $this->object        = $params['nodes'];
        $this->addable       = (bool)$params['addable'];
        $this->draggable     = $params['draggable'];
        $this->checkable     = $params['checkable'];
        $this->expandonstart = empty($params['expandonstart']) ? "false" : "true";

        // setup the controller for this..if it wasn't passed in we'll default to expTag
        $this->controller_classname = expModules::getControllerClassName(isset($params['controller']) ? $params['controller'] : 'expTag');
        $this->controller           = new $this->controller_classname();

        // check if a model name was passed in..if not we'll guess it from the controller
        $this->modelname = isset($params['model']) ? $params['model'] : $this->controller->basemodel_name;
        $this->model     = new $this->modelname();

        // get all the tags.
        $this->tags = $this->model->getFullTree();
        // eDebug($this->controller_classname);
    }

    function toHTML($label, $name) {
        $link = expCore::makeLink(array("module"=> $this->controller->baseclassname, "action"=> "edit", "parent"=> 0));
        $html = "";
        if ($this->menu == "true") {
            $framework = expSession::get('framework');
            if ($framework == 'bootstrap') {
                if (BTN_SIZE == 'large') {
                    $btn_size = 'btn-small';
                    $icon_size = 'icon-large';
                } else {
                    $btn_size = 'btn-mini';
                    $icon_size = '';
                }
                if ($this->addable) $html = '<a class="btn-success btn '.$btn_size.'" href="' . $link . '"><i class="icon-plus-sign '.$icon_size.'"></i> ' . gt('Add a Top Level Category') . '</a> ';
                $html .= '<a class="btn '.$btn_size.'" href="#" id="expandall"><i class="icon-resize-full '.$icon_size.'"></i> ' . gt('Expand All') . '</a> ';
                $html .= '<a class="btn '.$btn_size.'" href="#" id="collapseall"><i class="icon-resize-small '.$icon_size.'"></i> ' . gt('Collapse All') . '</a>';
            } else {
                if ($this->addable) $html = '<a class="add" href="' . $link . '">' . gt('Add a Top Level Category') . '</a> | ';
                $html .= '<a href="#" id="expandall">' . gt('Expand All') . '</a> | ';
                $html .= '<a href="#" id="collapseall">' . gt('Collapse All') . '</a>';
            }
        }

        $html .= '
		<div id="' . $this->id . '" class="nodetree"></div>
		<div class="loadingdiv">' . gt('Loading Categories') . '</div>';

        foreach ($this->tags as $i=> $val) {
            if (!empty($this->values) && in_array($val->id, $this->values)) {
                $this->tags[$i]->value = true;
            } else {
                $this->tags[$i]->value = false;
            }
            $this->tags[$i]->draggable = $this->draggable;
            $this->tags[$i]->checkable = $this->checkable;
        }

        $obj    = json_encode($this->tags);
//FIXME convert to yui3 because of call to exp-tree.js
        $script = "
		EXPONENT.YUI3_CONFIG.modules = {
               'exp-tree' : {
                   fullpath: EXPONENT.JS_RELATIVE+'exp-tree.js',
                   requires : ['node','yui2-container','yui2-menu','yui2-treeview','yui2-animation','yui2-dragdrop','yui2-json','yui2-connection']
               }
         }

  		//EXPONENT.YUI3_CONFIG.filter = \".js\";

            YUI(EXPONENT.YUI3_CONFIG).use('node','exp-tree', function(Y) {
    			var obj2json = " . $obj . ";
				EXPONENT.DragDropTree.init('" . $this->id . "',obj2json,'" . $this->modelname . "','" . $this->menu . "','" . $this->expandonstart . "');
				Y.one('.loadingdiv').remove();
			});
		";
//		exponent_javascript_toFoot('expddtree', 'treeview,menu,animation,dragdrop,json,container,connection', null, $script, JS_RELATIVE.'exp-tree.js');
        expJavascript::pushToFoot(array(
            "unique"  => 'expddtree',
            "yui3mods"=> 1,
            "content" => $script,
            //"src"=>JS_RELATIVE.'exp-tree.js'
        ));
        return $html;
    }

    function controlToHTML($name, $label) {
    }
}

?>
