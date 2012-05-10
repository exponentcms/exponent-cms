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
/** @define "BASE" "../../../../.." */

if (!defined('EXPONENT')) exit('');

/**
 * List Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class listcontrol extends formcontrol {

    var $html;
    var $span;
    
    static function name() { return "List Control"; }
    static function isSimpleControl() { return false; }
    
    function __construct() {
    }

    function toHTML($label,$name) {
        $html  = '<div id="list'.$name.'" class="list control">';
        $html .= '<label>'.gt('Add').' '.$label.'</label>';
        $html .= '<input id="list-input-'.$name.'" name="list-input-'.$name.'">';
        $html .= '<a class="addtolist" href="#">'.gt('Add to list').'</a>';
        $html .= '<h2>'.$label.'</h2>';
        $html .= '<ul id="list-values-'.$name.'">';
        
        if (count($this->value) > 0) {
            foreach ($this->value as $value) {
                $html .= '<li><input type="hidden" name="'.$name.'[]" value="'.$value.'">';
                $html .= $value.'<a class="remove-from-list" href="#">remove?</a></li>';
            }
        } else {
            '<h2 id="empty-list-'.$name.'">'.gt('There are no items yet.').'</h2>';
        }
        
        $html .= '</ul>';
        $html .= '</div>';
        
        $js = "
            var add = YAHOO.util.Dom.getElementsByClassName('addtolist', 'a');
            YAHOO.util.Event.on(add, 'click', function(e,o){
            YAHOO.util.Dom.setStyle('empty-list-".$name."', 'display', 'none');
            YAHOO.util.Event.stopEvent(e);
            var listitem = YAHOO.util.Dom.get('list-input-".$name."');
            var newli = document.createElement('li');
            var newLabel = document.createElement('span');
            newLabel.innerHTML = listitem.value + '<input type=\"hidden\" name=\"".$name."[]\" value=\"'+listitem.value+'\" />';
            var newRemove = document.createElement('a');
            newRemove.setAttribute('href','#');
            newRemove.innerHTML = ' ".gt('Remove')."?';
            newli.appendChild(newLabel);
            newli.appendChild(newRemove);
            var list = YAHOO.util.Dom.get('list-values-".$name."');
            list.appendChild(newli);
            YAHOO.util.Event.on(newRemove, 'click', function(e,o){
                var list = YAHOO.util.Dom.get('list-values-".$name."');
                list.removeChild(this)
            },newli,true);
            listitem.value = '';
            //alert(listitem);
            });
        
            var existingRems = YAHOO.util.Dom.getElementsByClassName('remove-from-list', 'a');
            YAHOO.util.Event.on(existingRems, 'click', function(e,o){
                YAHOO.util.Event.stopEvent(e);
                var targ = YAHOO.util.Event.getTarget(e);
                var lItem = YAHOO.util.Dom. getAncestorByTagName(targ,'li');
                var list = YAHOO.util.Dom.get('list-values-".$name."');
                list.removeChild(lItem);
            });
        "; // END PHP STRING LITERAL
        expJavascript::pushToFoot(array(
            "unique"=>"listcontrol".$name,
            "yui2mods"=>"json,connection",
            "yui3mods"=>"listcontrol-".$name,
            "content"=>$js,
            "src"=>""
         ));
        return $html;
    }
    
    function buildImages() {
        if (empty($this->value)) return null;

        //get the array of files
        $filearray = empty($this->subtype) ? $this->value : $this->value[$this->subtype];
        if (empty($filearray)) return null;
        
        $subTypeName = empty($this->subtype) ? "expFile[]" : "expFile[".$this->subtype."][]";
        // loop over each file and build out the HTML
        $cycle = "odd";
        foreach($filearray as $val) {
            if ($val->mimetype!="image/png" && $val->mimetype!="image/gif" && $val->mimetype!="image/jpeg"){
                $filepic = "<img class=\"filepic\" src='".ICON_RELATIVE."attachableitems/generic_22x22.png'>";
            } else {
                $filepic = "<img class=\"filepic\" src=".PATH_RELATIVE."thumb.php?id=".$val->id."&square=24\">";
            }
            $html = "<li class=\"".$cycle."\" id=\"imgdiv".$val->id."\">";
            $html .= "<input type=\"hidden\" name=\"".$subTypeName."\" value=\"".$val->id."\">";
            $html .= "<div class=\"fpdrag\"></div>";
            $html .= $filepic;
            $html .= "<div class=\"filename\">".$val->filename."</div>";
            $html .= "<a class=\"deletelinks\" rel=\"imgdiv".$val->id."\" href='javascript:{}'><img src='".ICON_RELATIVE."attachableitems/delete.png'></a>";
            $html .= "</li>";
            $cycle = $cycle=="odd" ? "even" : "odd";
        }
        
        return $html;
    }
    
    function controlToHTML($name,$label) {
        return $this->html;
    }
    
    function form($object) {
        $form = new form();
        if (!isset($object->html)) {
            $object->html = "";
        } 
        $form->register("html",'',new htmleditorcontrol($object->html));
        $form->register("submit","",new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
        return $form;
    }
    
    function update($values, $object) {
        if ($object == null) $object = new htmlcontrol();
        $object->html = preg_replace("/<br ?\/>$/","",trim($values['html']));
        $object->caption = '';
        $object->identifier = uniqid("");
        $object->is_static = 1;
        return $object;
    }
    
}

?>
