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

class formbuilder_report {
	static function form($object) {
		global $db;
		$form = new form();
        $form->is_tabbed = true;
		if (!isset($object->id)) {
			$object->name = '';
			$object->description = '';
			$object->text = '';
			$object->column_names = '';
		}
		
        $form->register(null,'',new htmlcontrol('<h2>'.gt('General Configuration').'</h2>'),true,gt('Report'));
		$form->register('name',gt('Title'),new textcontrol($object->name),true,gt('Report'));
		$form->register('description',gt('Description'),new texteditorcontrol($object->description),true,gt('Report'));

		$fields = array();
		$column_names = array();
		$cols = array();
		if ($object->column_names != '') {
			$cols = explode('|!|',$object->column_names);
		}
		if (isset($object->form_id)) {
			foreach ($db->selectObjects('formbuilder_control','form_id='.$object->form_id.' and is_readonly=0') as $control) {
				$ctl = unserialize($control->data);
				$control_type = get_class($ctl);
				$def = call_user_func(array($control_type,'getFieldDefinition'));
				if ($def != null) {
					$fields[$control->name] = $control->caption;
					if (in_array($control->name,$cols)) {
						$column_names[$control->name] = $control->caption;
					}
				}
			}
			$fields['ip'] = gt('IP Address');
			if (in_array('ip',$cols)) $column_names['ip'] = gt('IP Address');
	        
            if (isset($field['field_user_id']))
                $fields['user_id'] = $field['field_user_id'];

            if (in_array('user_id',$cols)) $column_names['user_id'] = gt('Username');
			$fields['timestamp'] = gt('Timestamp');
			if (in_array('timestamp',$cols)) $column_names['timestamp'] = gt('Timestamp');
		}
        $form->register(null,'',new htmlcontrol('<h2>'.gt('Data Field Configuration').'</h2>'),true,gt('Data Fields'));
        $form->register(null,'', new htmlcontrol(gt('Selecting NO columns is equal to selecting all columns')),true,gt('Data Fields'));
        $form->register('column_names',gt('Columns shown in View Data/Export CSV'), new listbuildercontrol($column_names,$fields),true,gt('Data Fields'));
		$form->register(null,'', new htmlcontrol(gt('Leave the below custom definition blank to use the default \'all fields\' e-mail report and record view.')),true,gt('Data Fields'));
		$form->register('text',gt('Custom E-Mail Report and View Record Definition'),new htmleditorcontrol($object->text),true,gt('Data Fields'));

        $form->register(null,null,new htmlcontrol('<div class="loadingdiv">'.gt('Loading Form Settings').'</div>'),true,'base');
		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel')),true,'base');

		return $form;
	}
	
	static function update($values, $object) {
		$object->name = $values['name'];
		$object->description = $values['description'];
		$object->text = htmleditorcontrol::parseData('text',$values);
		$object->column_names = $values['column_names'];
		return $object;
	}
}

?>