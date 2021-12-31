<?php

##################################################
#
# Copyright (c) 2004-20121 OIC Group, Inc.
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

//FIXME this is NOT a bootstrap control, but jQuery
/**
 * List Builder Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class listbuildercontrol extends formcontrol {

	var $source = null;
	var $size = 8;
	var $newList = false;
	var $process = null;

	static function name() { return "List Builder"; }

	function __construct($default,$source,$size=8) {
		if (is_array($default)) $this->default = $default;
        elseif(!empty($default)) $this->default = array($default);
        else $this->default = array();

		$this->size = $size;

		if ($source !== null) {
			if (is_array($source)) $this->source = $source;
			else $this->source = array($source);
		} else {
			$this->newList = true;
		}
	}

	function controlToHTML($name, $label, $process = null) {
		$this->process = $process;

		if (!$this->newList) {
			$html = "<select name='".$name."[]' id='$name' class=\"form-control\" multiple='multiple' size='$this->size'>";
            $list = $this->default + $this->source;
			foreach ($list as $key=>$value) {
				$html .= "<option value='$key'";
                if (array_key_exists($key,$this->default)) {
                    $html .= " selected='selected'";
                }
                $html .= ">$value</option>";
			}
			$html .= "</select>";
		} else {
            $this->_normalize();
			$html = '<input type="hidden" name="'.$name.'" id="'.$name.'" value="'.implode("|!|",array_keys($this->default)).'" />';
		    $html .= '<table cellpadding="9" border="0" width="70%" style="margin-bottom:0;"><tr><td width="40%" style="border:none;">';
			$html .= "<input id='source_$name' type='text' class=\"text form-control\"/>";
			$html .= "</td>";
			$html .= '<td valign="middle" width="10" style="border:none;">';
			if($process == "copy") {
				$html .= "<button type='button' class='btn btn-default' title='".gt('Add to list')."' onclick='addSelectedItem(&quot;$name&quot;,&quot;copy&quot;); return false' ><i class='fa fa-fw fa-arrow-right'></i><i class='fa fa-fw fa-arrow-right'></i></button>";
			} else {
				$html .= "<button type='button' class='btn btn-default' title='".gt('Add to list')."' onclick='addSelectedItem(&quot;$name&quot;); return false' ><i class='fa fa-fw fa-arrow-right'></i><i class='fa fa-fw fa-arrow-right'></i></button>";
			}
			$html .= "<br />";
			if($process == "copy") {
				$html .= "<button type='button' class='btn btn-default' title='".gt('Remove from list')."' onclick='removeSelectedItem(&quot;$name&quot;,&quot;copy&quot); return false;' ><i class='fa fa-fw fa-arrow-left'></i><i class='fa fa-fw fa-arrow-left'></i></button>";
			} else {
				$html .= "<button type='button' class='btn btn-default' title='".gt('Remove from list')."' onclick='removeSelectedItem(&quot;$name&quot;); return false;' ><i class='fa fa-fw fa-arrow-left'></i><i class='fa fa-fw fa-arrow-left'></i></button>";
			}
			$html .= "</td>";
			$html .= "<td width='40%' valign='top' style='border:none;'><div class=\"".(bs3()?"help-block":"control-desc")."\">".gt('Selected')."</div><select " . (bs3() ? "class=\"form-control\" " : "") . "id='dest_$name' size='".$this->size."'>";
			foreach ($this->default as $key=>$value) {
				if (isset($this->source[$key])) $value = $this->source[$key];
				$html .= "<option value='$key'>$value</option>";
			}
			$html .= "</select>";
			$html .= "</td><td width='20%' style='border:none;'></td></tr></table>";
//			$html .= "<script>newList.$name = ".($this->newList?"true":"false").";</script>";
		}
        $html .= "<div style=\"clear:both\"></div>";
        if (!empty($this->description)) $html .= "<div class=\"".(bs3()?"help-block":"control-desc")."\">".$this->description."</div>";
		return $html;
	}

    function toHTML($label,$name) {
		if (!empty($this->id)) {
		    $divID  = ' id="'.$this->id.'Control"';
		    $for = ' for="'.createValidId($this->id).'"';
		} else {
		    $divID  = '';
		    $for = '';
		}

		$disabled = $this->disabled != 0 ? "disabled" : "";
		$class = empty($this->class) ? '' : $this->class;

		$html = "<div".$divID." class=\"".$this->type."-control control form-group ".$class.$disabled;
		$html .= !empty($this->required) ? ' required">' : '">';
		//$html .= "<label>";
        if($this->required) {
            $labeltag = '<span class="required" title="'.gt('This entry is required').'">*&#160;</span>' . $label;
        } else {
            $labeltag = $label;
        }
		$process = empty($this->process) ? null : $this->process;
		if(empty($this->flip)){
			$html .= (!empty($label)) ? "<label".$for." class=\"".(bs3()?"control-label":"label")."\">".$labeltag."</label>" : "";
			$html .= $this->controlToHTML($name, $label, $process);
		} else {
			$html .= $this->controlToHTML($name, $label, $process);
			$html .= (!empty($label)) ? "<label".$for." class=\"".(bs3()?"control-label":"label")."\">".$labeltag."</label>" : "";
		}
		//$html .= "</label>";
		$html .= "</div>";
        if (!$this->newList) {
            expJavascript::pushToFoot(array(
                "unique"=>'jquery-multiselect',
                "jquery"=>"jquery.bootstrap-duallistbox",
            ));
            $src = "
                $(document).ready(function() {
                    $('#$name').bootstrapDualListbox({
                        nonSelectedListLabel: 'Non-selected',
                        selectedListLabel: 'Selected',
                        sortByInputOrder: true,
//                        preserveSelectionOnMove: 'moved',
//                        moveOnSelect: false,
                    });
                });
            ";
            expJavascript::pushToFoot(array(
                "unique"=>"source_$name",
                "content"=>$src,
                "jquery"=>"1",
            ));
        } else {
            expJavascript::pushToFoot(array(
                "unique"=>'listbuildercontrol',
                "src"=> PATH_RELATIVE . 'framework/core/forms/controls/listbuildercontrol.js'
    		));
        }
        return $html;
    }

	// Normalizes the $this->source and $this->defaults array
	// This allows us to gracefully recover from _formErrors and programmer error
	function _normalize() {
		if (!$this->newList) { // Only do normalization if we are not creating a list from scratch.
			// First, check to see if our parent has flipped the inError attribute to 1.
			// If so, we need to normalize the $this->default based on the source.
			if ($this->inError == 1) {
				$default = array();
				foreach ($this->default as $id) {
					$default[$id] = $this->source[$id];
					// Might as well normalize $this->source while we are here
					unset($this->source[$id]);
				}
				$this->default = $default;
			} else {
				// No form Error.  Just normalize $this->source
				if($this->process != 'copy') {
					$this->source = array_diff_assoc($this->source,$this->default);
				}
			}
		}
	}

	function onRegister(&$form) {
        if ($this->newList) {
		    $form->addScript("listbuildercontrol",PATH_RELATIVE."framework/core/forms/controls/listbuildercontrol.js");
        }
	}

    static function parseData($name, $values, $forceindex = false) { // 3rd param normally $for_db
		$retvalues = array();
		if ($values[$name] == "")
		    return array();
        if (is_array($values[$name])) {
            if (!$forceindex) {
                $retvalues = $values[$name];
            } else {
                foreach ($values[$name] as $value) {
                    $retvalues[$value] = $value;
                }
            }
        } else {
            foreach (explode("|!|",$values[$name]) as $value) {
                if ($value != "") {
                    if (!$forceindex) {
                        $retvalues[] = $value;
                    }
                    else {
                        $retvalues[$value] = $value;
                    }
                }
            }
        }
		return $retvalues;
	}

	static function form($object) {
		$form = new form();
		if (!isset($object->identifier)) {
			$object->identifier = "";
			$object->caption = "";
		}
		$form->register("identifier",gt('Identifier/Field'),new textcontrol($object->identifier),true, array('required'=>true));
		$form->register("caption",gt('Caption'), new textcontrol($object->caption));
		if (!expJavascript::inAjaxAction())
			$form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
		return $form;
	}

    static function update($values, $object) {
		if ($values['identifier'] == "") {
			$post = expString::sanitize($_POST);
			$post['_formError'] = gt('Identifier is required.');
			expSession::set("last_POST",$post);
			return null;
		}
		$object->identifier = $values['identifier'];
		$object->caption = $values['caption'];
		return $object;
	}

}

?>
