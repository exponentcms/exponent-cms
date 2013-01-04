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
/** @define "BASE" "../../../.." */

if (!defined('EXPONENT')) exit('');

$template = new template('formbuilder','_data_view');

if (isset($_GET['id'])) {
	$_GET['id'] = intval($_GET['id']);
	
	$f = $db->selectObject("formbuilder_form","id=".$_GET['id']);
	$rpt = $db->selectObject("formbuilder_report","form_id=".$_GET['id']);
	$items = $db->selectObjects("formbuilder_".$f->table_name);
	if (expPermissions::check("viewdata",unserialize($f->location_data))) {
		expHistory::set('editable', $_GET);
		$columndef = "paginate.columns = new Array(";
		$columns = array();
		$sortfuncts = "";
		if ($rpt->column_names == '') {
			//define some default columns...
			$controls = $db->selectObjects("formbuilder_control","form_id=".$f->id." and is_readonly = 0 and is_static = 0","rank");
//			$controls = expSorter::sort(array('array'=>$controls,'sortby'=>'rank', 'order'=>'ASC'));

			foreach (array_slice($controls,0,5) as $control) {
				if ($rpt->column_names != '') $rpt->column_names .= '|!|';
				$rpt->column_names .= $control->name;
			}
		}

		foreach (explode("|!|",$rpt->column_names) as $column_name) {
			if ($column_name == "ip") {
				$columndef .= 'new cColumn("'.gt('IP Address').'","ip",null,null),';
				$columns[gt('IP Address')] = 'ip';
			} elseif ($column_name == "user_id") {
				foreach ($items as $key=>$item) {
					if ($item->$column_name != 0) {
						 $locUser = user::getUserById($item->$column_name);
						 $item->$column_name = $locUser->username;
					} 
					else {
						$item->$column_name = '';
					}
					$items[$key] = $item;
				}
				$columndef .= 'new cColumn("'.gt('Username').'","user_id",null,null),';
				$columns[gt('Username')] = 'user_id';
			} elseif ($column_name == "timestamp") {
				$srt = $column_name."_srt";
				foreach ($items as $key=>$item) {
					$item->$srt = $item->$column_name;
					$item->$column_name = strftime(DISPLAY_DATETIME_FORMAT,$item->$column_name);
					$items[$key] = $item;
				}
				$columndef .= 'new cColumn("'.gt('Timestamp').'","timestamp",null,f'.$srt.'),';
				$columns[gt('Timestamp')] = 'timestamp';
				$sortfuncts .= 'function f'.$srt.'(a,b) {return (a.var_'.$srt.'<b.var_'.$srt.')?1:-1;}';
			
			} else {
				$control = $db->selectObject("formbuilder_control","name='".$column_name."' and form_id=".$_GET['id']);
				if ($control) {
					$ctl = unserialize($control->data);
					$control_type = get_class($ctl);
					$srt = $column_name."_srt";
					$datadef = call_user_func(array($control_type,'getFieldDefinition'));
					foreach ($items as $key=>$item) {
						//We have to add special sorting for date time columns!!!
						if (isset($datadef[DB_FIELD_TYPE]) && $datadef[DB_FIELD_TYPE] == DB_DEF_TIMESTAMP) {
							$item->$srt = @$item->$column_name;
						}
						$item->$column_name = @call_user_func(array($control_type,'templateFormat'),$item->$column_name,$ctl);
						$items[$key] = $item;
					}
					if (isset($datadef[DB_FIELD_TYPE]) && $datadef[DB_FIELD_TYPE] == DB_DEF_TIMESTAMP) {
						$columndef .= 'new cColumn("' . $control->caption . '","'.$column_name.'",null,f'.$srt.'),';
						$columns[$control->caption] = $column_name;
						$sortfuncts .= 'function f'.$srt.'(a,b) {return (a.var_'.$srt.'<b.var_'.$srt.')?1:-1;}';
					} else {
						$columndef .= 'new cColumn("' . $control->caption . '","'.$column_name.'",null,null),';
						$columns[$control->caption] = $column_name;
					}
				}
			}
		}
		
//		$template->assign("items",$items);
		$template->assign("f",$f);
		$template->assign("backlink",expHistory::getLastNotEditable());
		$template->register_permissions(array("manage","editform","editformsettings","editreport","viewdata","editdata","deletedata"),unserialize($f->location_data));
		$columndef .= 'new cColumn("Links","",links,null)';
		$columndef .= ');';
		
//		$template->assign('columdef',$columndef);
//		$template->assign('sortfuncs',$sortfuncts);
        // we left expPaginator filter out unneeded columns
        $page = new expPaginator(array(
//                    'model'=>$f->table_name,
            'records'=>$items,
            'where'=>1,
            'limit'=>(isset($_GET['limit']) && $_GET['limit'] != '') ? $_GET['limit'] : 10,
            'order'=>(isset($_GET['order']) && $_GET['order'] != '') ? $_GET['order'] : 'id',
            'dir'=>(isset($_GET['dir']) && $_GET['dir'] != '') ? $_GET['dir'] : 'ASC',
//                    'order'=>$order,
            'page'=>(isset($_GET['page']) ? $_GET['page'] : 1),
            'action'=>'view_data',
            'columns'=>$columns
        ));
		$template->assign('page',$page);			
		$template->assign('title',$rpt->name);
		$template->output();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>