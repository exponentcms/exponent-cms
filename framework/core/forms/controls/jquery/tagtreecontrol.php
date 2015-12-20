<?php

##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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

    var $values = array();
    var $menu = true;
    var $addable = true;
    var $draggable = true;
    var $checkable = true;
    var $expandonstart = true;
    var $controller_classname = null;
    var $controller = null;
    var $modelname = null;
    var $model = null;
    var $tags = array();

    static function name() {
        return "Nested Node Checkbox Dragdrop Tree";
    }

    static function getFieldDefinition() {
        return array();
    }

    function __construct($params) {
//        global $db;

        if (!empty($params['values'])) {
            foreach ($params['values'] as $key=> $var) {
                $this->values[$key] = $var->id;
            }
        }

        $this->object        = $params['nodes'];
        $this->menu          = !empty($params['menu']) ? true : false;
        $this->addable       = (bool)$params['addable'];
        $this->draggable     = $params['draggable'];
        $this->checkable     = $params['checkable'];
        $this->expandonstart = empty($params['expandonstart']) ? false : true;

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
//        if ($this->menu == "true") {
            if (bs3()) {
                $btn_size = expTheme::buttonSize();
                $icon_size = expTheme::iconSize();
                if ($this->addable) $html = '<a class="btn-success btn '.$btn_size.'" href="' . $link . '"><i class="fa fa-plus-circle '.$icon_size.'"></i> ' . gt('Add a Top Level Category') . '</a> ';
//                $html .= '<a class="btn btn-default '.$btn_size.'" href="#" id="refresh-tree"><i class="fa fa-refresh '.$icon_size.'"></i> ' . gt('Refresh') . '</a> ';
                $html .= '<a class="btn btn-default '.$btn_size.'" href="#" id="expand-tree"><i class="fa fa-expand '.$icon_size.'"></i> ' . gt('Expand All') . '</a> ';
                $html .= '<a class="btn btn-default '.$btn_size.'" href="#" id="collapse-tree"><i class="fa fa-compress '.$icon_size.'"></i> ' . gt('Collapse All') . '</a>';
            } elseif (bs2()) {
                $btn_size = expTheme::buttonSize();
                $icon_size = expTheme::iconSize();
                if ($this->addable) $html = '<a class="btn-success btn '.$btn_size.'" href="' . $link . '"><i class="icon-plus-sign '.$icon_size.'"></i> ' . gt('Add a Top Level Category') . '</a> ';
                $html .= '<a class="btn '.$btn_size.'" href="#" id="expand-tree"><i class="icon-resize-full '.$icon_size.'"></i> ' . gt('Expand All') . '</a> ';
                $html .= '<a class="btn '.$btn_size.'" href="#" id="collapse-tree"><i class="icon-resize-small '.$icon_size.'"></i> ' . gt('Collapse All') . '</a>';
            } else {
                if ($this->addable) $html = '<a class="add" href="' . $link . '">' . gt('Add a Top Level Category') . '</a> | ';
                $html .= '<a href="#" id="expand-tree">' . gt('Expand All') . '</a> | ';
                $html .= '<a href="#" id="collapse-tree">' . gt('Collapse All') . '</a>';
            }
//        }

        $icon = array(
            'add' => '',
            'view' => '',
            'edit' => '',
            'configure' => '',
            'delete' => ''
        );
        foreach ($icon as $key=>$icn) {
            $text = expTheme::buttonIcon($key, 'large');
            $icon[$key] = $text->prefix . $text->class . ' ' . $text->size;
            if (bs3())
                $icon[$key] .= ' fa-fw';
            elseif (bs2())
                $icon[$key] .= ' icon-fixed-width';
        }

        $html .= '
		<div id="' . $this->id . '" class="nodetree"></div>
		<div id="' . $this->id . '-checks"></div>';

        foreach ($this->tags as $i=> $val) {
            if (!empty($this->values) && in_array($val->id, $this->values)) {
                $this->tags[$i]->value = true;
                $this->tags[$i]->state->selected = true;
            } else {
                $this->tags[$i]->value = false;
                $this->tags[$i]->state->selected = false;
            }
            $this->tags[$i]->draggable = $this->draggable;
            $this->tags[$i]->checkable = $this->checkable;
        }

        $obj    = json_encode($this->tags);
        if ($this->menu) {
            $menu = "

        ";
        }
        $script = "
    $(document).ready(function(){
        var obj2json = " . $obj . ";
        var tmp = { node : null, pos : null };

        $('#" . $this->id . "').jstree({
            'core' : {
                'data' : obj2json,
                'check_callback' : function (op, node, parent, position, more) {
                    if((op === 'move_node' || op === 'copy_node') && more && more.dnd) {
                        tmp.node = more.ref;
                        tmp.pos = more.pos;
                    }
                },
                'themes' : {
                    'name': 'proton',
                    'responsive' : true,
                    'url' : true,
                    'dots' : false,
                    'variant' : 'small',
                },
                'strings' : {
                    'Loading ...' : '" . gt('Loading Categories') . " ...'
                }
            },
            'contextmenu' : {
                'items' : {" . ($this->addable?"
                    'addone' : {
                        'icon'				: '" . $icon['add'] . " text-success',
                        'label'				: '" . gt('Add a Sub-Category') . "',
                        'action'			: function (data) {
                                                  var inst = $.jstree.reference(data.reference),
                                                  obj = inst.get_node(data.reference);
                                                  window.location=eXp.PATH_RELATIVE+'index.php?module=" . $this->controller->baseclassname . "&action=adsubnode&id='+obj.id;
                                              }
                    },
                    'viewone' : {
                        'icon'				: '" . $icon['view'] . "',
                        'label'				: '" . gt('View this Category') . "',
                        'action'			: function (data) {
                                                  var inst = $.jstree.reference(data.reference),
                                                  obj = inst.get_node(data.reference);
                                                  window.location=obj.original.href;
                                              }
                    },
                    ":"") . "
                    'editone' : {
                        'icon'				: '" . $icon['edit'] . "',
                        'label'				: '" . gt('Edit this Category') . "',
                        'action'			: function (data) {
                                                  var inst = $.jstree.reference(data.reference),
                                                  obj = inst.get_node(data.reference);
                                                  window.location=eXp.PATH_RELATIVE+'index.php?module=" . $this->controller->baseclassname . "&action=edit&id='+obj.id;
                                              }
                    },
                    'configureone' : {
                        'icon'				: '" . $icon['configure'] . "',
                        'label'				: '" . gt('Configure this Category') . "',
                        'action'			: function (data) {
                                                  var inst = $.jstree.reference(data.reference),
                                                  obj = inst.get_node(data.reference);
                                                  window.location=eXp.PATH_RELATIVE+'index.php?module=" . $this->controller->baseclassname . "&action=configure&id='+obj.id;
                                              }
                    }," . ($this->addable?"
                    'deleteone' : {
                        'icon'				: '" . $icon['delete'] . " text-danger',
                        'label'				: '" . gt('Delete this Category') . "',
                        'action'			: function (data) {
                                                  var inst = $.jstree.reference(data.reference),
                                                  obj = inst.get_node(data.reference);
                                                  window.location=eXp.PATH_RELATIVE+'index.php?module=" . $this->controller->baseclassname . "&action=delete&id='+obj.id;
                                              }
                    },
                    ":"") . "
                }
            },
            'checkbox' : {
                'keep_selected_style' : false,
                'three_state' : false,
//                'whole_node' : false,
                'cascade' : 'undetermined'
            },
            'plugins' : [" . ($this->draggable?"'dnd'":"") . "," . ($this->menu?"'contextmenu'":"") . "," . ($this->checkable?"'checkbox'":"") . "]
        }).on('move_node.jstree', function (e, data) {
            $.post(eXp.PATH_RELATIVE+'index.php?ajax_action=1&module=" . $this->controller->baseclassname . "&action=reorder', { 'move' : data.node.id, 'target' : tmp.node.id, 'type' : tmp.pos })
                .fail(function () {
                    data.instance.refresh();
                });
//        }).on('select_node.jstree', function (e, data) {  // selecting a node opens/closes it
//            data.instance.toggle_node(data.node);
        });

        $('#refresh-tree').on('click', function(){
            $('#" . $this->id . "').jstree().refresh();
        });
        $('#expand-tree').on('click', function(){
            $('#" . $this->id . "').jstree().open_all();
        });
        $('#collapse-tree').on('click', function(){
            $('#" . $this->id . "').jstree().close_all();
        });
        " . ($this->expandonstart?"$('#" . $this->id . "').on('ready.jstree',  function(){
            $('#" . $this->id . "').jstree().open_all();
        });":"") . "
        " . ($this->checkable?"$('#" . $this->id . "').on('changed.jstree',  function(){
            var model = '" .  $this->modelname . "';
            var inputs = '';
            $.each($('#" . $this->id . "').jstree().get_checked(), function(index, value) {
                inputs += '<input type=\"hidden\" id=\"jsfields\" name=\"' + model + '[]\" value=\"' + value + '\" />';
            });
            $('#" . $this->id . "-checks').html(inputs);
        });":"") . "
    })
        ";
        expJavascript::pushToFoot(array(
            "unique"  => 'expddtree',
            "jquery"=> 'jstree',
            "content" => $script,
        ));
        return $html;
    }

    function controlToHTML($name, $label) {
    }
}

?>
