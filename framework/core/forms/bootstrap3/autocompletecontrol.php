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
/** @define "BASE" "../../.." */

if (!defined('EXPONENT')) exit('');

/**
 * Auto-complete Control
 *
 * @package Subsystems-Forms
 * @subpackage Control
 */
class autocompletecontrol extends formcontrol {

    static function name() { return "YAHOO! UI Autocomplete"; }

	function toHTML($label,$name) {
		$html = $this->controlToHTML($name, $label);
		return $html;
	}

    function controlToHTML($name,$label) {
    	$assets_path = SCRIPT_RELATIVE.'framework/core/forms/controls/assets/';
        $html = '<div class="text-control control exp-skin" id="search_stringControl">';
        $html .= empty($this->label) ? '' : '<label for="'.$name.'">'.$label.'</label>';
        $framework = expSession::get('framework');
        if ($framework == 'bootstrap') {
            $html .= '<div class="input-prepend">';
            $html .= '<span class="add-on"><i class="icon-search"></i></span>';
        }
        $html .= '<input type="search" class="text " size="20" value="'.$this->value.'" name="'.$name.'" id="'.$name.'"/>';
        if ($framework == 'bootstrap') {
            $html .= '</div>';
        }
        $html .= '<div id="results'.$name.'"></div>
            </div>
        ';
        
//FIXME convert to yui3
        $script = "
        YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-yahoo-dom-event','yui2-animation','yui2-autocomplete','yui2-connection','yui2-datasource', function(Y) {
            YAHOO = Y.YUI2;
            Y.one('#".$name."').on('click',function(e){e.target.set('value','');});

            // autocomplete
            var autocomplete = function() {
                // Use an XHRDataSource
                var oDS = new YAHOO.util.XHRDataSource(EXPONENT.PATH_RELATIVE+\"index.php\");
                // Set the responseType
                oDS.responseType = YAHOO.util.XHRDataSource.TYPE_JSON;

                //oDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
                // Define the schema of the delimited results
                oDS.responseSchema = {
                    resultsList : \"data\",
                    fields : [".$this->schema."]
                };

                // Enable caching
                oDS.maxCacheEntries = 5;

                // Instantiate the AutoComplete
                var oAC = new YAHOO.widget.AutoComplete(\"".$name."\", \"results".$name."\", oDS);
                oAC.generateRequest = function(sQuery) {
                    return \"?ajax_action=1&json=1&controller=".$this->controller."&model=".$this->searchmodel."&searchoncol=".$this->searchoncol."&action=".$this->action."&query=\"+sQuery ;
                };
                
                ".$this->jsinject."

            }();
        });
        "; // end JS
        
        // css
        expCSS::pushToHead(array(
    	    "unique"=>"autocomplete",
    	    "link"=>$assets_path."autocomplete/autocomplete.css"
    	    )
    	);
	
        expJavascript::pushToFoot(array(
            "unique"=>'ac'.$name,
            "yui3mods"=>1,
            "content"=>$script,
            "src"=>""
         ));
       
        //exponent_javascript_toFoot('ac'.$name, "animation,autocomplete,connection,datasource", null, $script);
        return $html;
    }

}

?>
