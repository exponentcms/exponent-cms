<?php

##################################################
#
# Copyright (c) 2004-2017 OIC Group, Inc.
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
 * Tag Picker Control using jQuery tag-it
 *
 * @package    Subsystems-Forms
 * @subpackage Control
 */
class tagpickercontrol extends formcontrol {

    var $record = array();
    var $subtype = '';
    var $taglist = '';

    static function name() {
        return "Tag Picker";
    }

//    static function getFieldDefinition() {
//        return array();
//    }

    function __construct($collections = array(), $subtype = null) {
//        global $db;

//		$this->tags = $db->selectNestedTree('expTags');
//        $this->tags    = $db->selectObjects('expTags', 1);
//        $tags = $db->selectObjects('expTags','1','title ASC');
//   		$this->taglist = '';
//        foreach ($tags as $tag) {
//            $this->taglist .= "'".$tag->title."',";
//        }
        $this->taglist = expTag::getAllTags();
        $this->subtype = isset($subtype) ? $subtype : '';
    }

    function toHTML($label, $name) {
        if (empty($this->class)) $this->class = "tagpicker";
        if (empty($name)) $name = 'expTag';
        $this->id    = (!empty($this->id)) ? $this->id : $name;
        if (empty($label)) $label = gt('Tags (comma separated)');

//        $html        = "<div id=\"" . $this->id . "Control\" class=\"control " . $this->class . "";
//        $html .= (!empty($this->required)) ? ' required">' : '">';
//        $html .= "<label>";
//        if (empty($this->flip)) {
//            $html .= "<span class=\"label\">" . $label . "</span>";
            $html = $this->controlToHTML($name, $label);
//        } else {
//            $html .= $this->controlToHTML($name, $label);
//            $html .= "<span class=\"label\">" . $label . "</span>";
//        }
//        $html .= "</label>";
//        $html .= "</div>";
        return $html;
    }

    function controlToHTML($name, $label) {
        $this->name = !empty($this->name) ? $this->name : $name;
        $this->id   = !empty($this->id) ? $this->id : $name;

        $this->record  = $this->default;
        $selectedtags = '';
        foreach ($this->record->expTag as $tag) {
            $selectedtags .= $tag->title . ', ';
        }
//        $textbox = new genericcontrol('text');
        $textbox = new textcontrol();
        $textbox->id = $this->id;
        $textbox->name = $this->id;
        $textbox->default = $selectedtags;
        $textbox->size = 45;
        $textbox->flip    = $this->flip;
        $textbox->required    = $this->required;
        $textbox->disabled    = $this->disabled;
        $textbox->class    = $this->class;

        expJavascript::pushToFoot(array(
            "unique"  => 'tag-it',
            "jquery"=> 'jqueryui,tag-it',
        ));
        $script = "
            $('#" . $this->id . "').tagit({
                availableTags: [" . $this->taglist . "],
                allowSpaces: true
            });
        ";
        expJavascript::pushToFoot(array(
            "unique"  => 'exptag-' . $name,
            "jquery"=> 1,
            "content" => $script,
        ));

        return $textbox->toHTML($label, $name);
    }
}

?>
