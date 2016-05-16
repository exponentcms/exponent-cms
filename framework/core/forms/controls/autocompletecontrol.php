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
/** @define "BASE" "../../.." */

if (!defined('EXPONENT')) exit('');

/**
 * Auto-complete Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class autocompletecontrol extends formcontrol {

    var $placeholder = "";
    var $width = "320px";
    var $controller = "";   // controller to call
    var $action = "";       // action to call
    var $searchmodel = "";  // model to search
    var $searchoncol = "";  // model columns to search on
    var $maxresults = 12;   // number of returns to display
    var $jsinject = "";     // additional javascript code such as event traps, etc...

    static function name() { return "YAHOO! UI Autocomplete"; }

	function toHTML($label,$name) {
		$html = $this->controlToHTML($name, $label);
		return $html;
	}

    function controlToHTML($name,$label) {
        $html = '<div class="yui3-skin-sam" style="z-index: 999;">';
        $ac_input = new genericcontrol();
        $ac_input->type = 'search';
        $ac_input->id = $name  . '_autoc';
        $ac_input->class = 'text';
        $ac_input->prepend = 'search';
        $ac_input->placeholder = $this->placeholder;
        $html .= $ac_input->toHTML(null, "$name");
        $html .= '</div>';

        $script = "
        YUI(EXPONENT.YUI3_CONFIG).use('*', function (Y) {
            var autocomplete = Y.one('#".$name."_autoc');
            autocomplete.plug(Y.Plugin.AutoComplete, {
                width:'320px',
                maxResults: ".$this->maxresults.",
                resultListLocator: 'data',  // 'data' field of json response
                resultTextLocator: 'title', // the field to place in the input after selection
                source: EXPONENT.PATH_RELATIVE+'index.php?controller=".$this->controller."&action=".$this->action."&json=1&ajax_action=1',
                requestTemplate: '&query={query}'
            });

            // display 'loading' icon
            autocomplete.ac.on('query', function (e) {
                Y.one('#".$name."_autocControl span i').removeClass('".expTheme::iconStyle('search')."').addClass('".expTheme::iconStyle('ajax')."');
            });

            // display regular icon
            autocomplete.ac.on('results', function (e) {
                Y.one('#".$name."_autocControl span i').removeClass('".expTheme::iconStyle('ajax')."').addClass('".expTheme::iconStyle('search')."');
            });

            ".$this->jsinject."
        });
        "; // end JS

        expCSS::pushToHead(array(
    	    "unique"=>"autocompletecontrol$name",
    	    "css"=>"
                .yui3-aclist {
                    z-index: 99!important;
                    overflow-x: auto;
                }
                #".$name."_autoc {
                    width: ".$this->width.";
                }
    	    "
    	    )
    	);

        expJavascript::pushToFoot(array(
            "unique"=>'ac'.$name,
            "yui3mods"=>"autocomplete,autocomplete-highlighters",
            "content"=>$script,
         ));

        return $html;
    }

}

?>
