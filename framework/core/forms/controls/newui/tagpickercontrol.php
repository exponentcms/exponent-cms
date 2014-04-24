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

if (!defined('EXPONENT')) exit('');

/**
 * Tag Picker Control
 *
 * @package    Subsystems-Forms
 * @subpackage Control
 */
class tagpickercontrol extends formcontrol {

    var $flip = false;
    var $jsHooks = array();
    var $record = array();
    var $taglist = '';

    static function name() {
        return "Tag Picker";
    }

    static function getFieldDefinition() {
        return array();
    }

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
        $textbox = new genericcontrol('text');
        $textbox->id = 'expTag';
        $textbox->name = 'expTag';
        $textbox->default = $selectedtags;
        $textbox->size = 45;
        $textbox->flip    = $this->flip;
        $textbox->required    = $this->required;
        $textbox->disabled    = $this->disabled;
        $textbox->class    = $this->class;

        $script = "
            YUI(EXPONENT.YUI3_CONFIG).use('autocomplete','autocomplete-filters','autocomplete-highlighters',function(Y) {
                var inputNode = Y.one('#expTag');
                var tags = [".$this->taglist."];

                inputNode.plug(Y.Plugin.AutoComplete, {
                  activateFirstItem: true,
                  allowTrailingDelimiter: true,
                  minQueryLength: 0,
                  queryDelay: 0,
                  queryDelimiter: ',',
                  source: tags,
                  resultHighlighter: 'phraseMatch',

                  // Chain together a phraseMatch filter followed by a custom result filter
                  // that only displays tags that haven't already been selected.
                  resultFilters: ['phraseMatch', function (query, results) {
                    // Split the current input value into an array based on comma delimiters.
                    var selected = inputNode.ac.get('value').split(/\s*,\s*/);

                    // Pop the last item off the array, since it represents the current query
                    // and we don't want to filter it out.
                    selected.pop();

                    // Convert the array into a hash for faster lookups.
                    selected = Y.Array.hash(selected);

                    // Filter out any results that are already selected, then return the
                    // array of filtered results.
                    return Y.Array.filter(results, function (result) {
                      return !selected.hasOwnProperty(result.text);
                    });
                  }]
                });

                // When the input node receives focus, send an empty query to display the full
                // list of tag suggestions.
                    inputNode.on('focus', function () {
                    inputNode.ac.sendRequest('');
                });

                // After a tag is selected, send an empty query to update the list of tags.
                inputNode.ac.after('select', function () {
                    inputNode.ac.sendRequest('');
                    inputNode.ac.show();
                });
            });
        ";

        expJavascript::pushToFoot(array(
            "unique"  => 'exptag-' . $name,
            "yui3mods"=> 1,
            "content" => $script,
        ));

        return $textbox->toHTML($label, $name);
    }
}

?>
