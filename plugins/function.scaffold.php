<?php

##################################################
#
# Copyright (c) 2007-2008 OIC Group, Inc.
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

function smarty_function_scaffold($params,&$smarty) {
	if (isset($params['model']) ) {
		if (!defined('SYS_FORMS')) require_once(BASE.'subsystems/forms.php');
        	//exponent_forms_initialize();
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
			        $ctl['type'] = exponent_forms_guessControlType($col, $default_value, $key);
		        }

		        //format the values if needed
		        if (isset($col[FORM_FIELD_FILTER])) {
            		switch ($col[FORM_FIELD_FILTER]) {
		            	case MONEY:
               			case DECIMAL_MONEY:
            	    		$ctl['value'] = exponent_core_getCurrencySymbol('USD') . number_format($ctl['value'],2,'.',',');
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

	$submit = new buttongroupcontrol('Submit', 'Reset', 'Cancel'); 
	echo $submit->controlToHTML('submit');
}

?>
