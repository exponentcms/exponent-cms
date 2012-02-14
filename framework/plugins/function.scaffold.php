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

/**
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {scaffold} function plugin
 *
 * Type:     function<br>
 * Name:     scaffold<br>
 * Purpose:  scaffold
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_scaffold($params,&$smarty) {
	if (isset($params['model']) ) {
		global $db;
		require_once $smarty->_get_plugin_filepath('function','control');

		$table = $db->getDataDefinition($params['model']);
    	foreach ($table as $key=>$col) {
		    if ($key != 'created_at' && $key != 'edited_at' && $key != 'poster' && $key != 'editor' && $key != 'location_data') {
		        $ctl = array();
		        //Get the default value
		        if ( isset($params['item']) ) $ctl['value'] = isset($params['item']->$key) ? $params['item']->$key : "";

		        //Get the base control
		        if ($key == 'id') {
			        $ctl['type'] = 'hidden';
		        } else {
			        $ctl['type'] = expTemplate::guessControlType($col, $default_value, $key);
		        }

		        //format the values if needed
		        if (isset($col[FORM_FIELD_FILTER])) {
            		switch ($col[FORM_FIELD_FILTER]) {
		            	case MONEY:
               			case DECIMAL_MONEY:
            	    		$ctl['value'] = expCore::getCurrencySymbol('USD') . number_format($ctl['value'],2,'.',',');
              	    		$ctl['filter'] = 'money';
               			break;
           			}
        		}

		        //write out the control itself...and then we're done. 
		        if (isset($col[FORM_FIELD_ONCLICK])) $ctl['onclick'] = $col[FORM_FIELD_ONCLICK];
		        $ctl['label'] = isset($col[FORM_FIELD_LABEL]) ? $col[FORM_FIELD_LABEL] : $key;
		        $ctl['name'] = isset($col[FORM_FIELD_NAME]) ? $col[FORM_FIELD_NAME] : $key;
		        echo smarty_function_control($ctl, $smarty);
		        //echo $control->controlToHTML($control_label, $control_name);
			}
		}
    }

	$submit = new buttongroupcontrol(gt('Submit'), gt('Reset'), gt('Cancel'));
	echo $submit->controlToHTML('submit');
}

?>
