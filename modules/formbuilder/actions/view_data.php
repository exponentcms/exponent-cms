<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
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
if (!defined("EXPONENT")) exit("");

$i18n = exponent_lang_loadFile('modules/formbuilder/actions/view_data.php');

//if (!defined('SYS_FORMS')) include_once(BASE.'subsystems/forms.php');
//if (!defined('SYS_USERS')) include_once(BASE.'subsystems/users.php');
include_once(BASE.'subsystems/forms.php');
include_once(BASE.'subsystems/users.php');
//exponent_forms_initialize();

$template = new template('formbuilder','_data_view');

if (isset($_GET['id'])) {
	$_GET['id'] = intval($_GET['id']);
	
	$f = $db->selectObject("formbuilder_form","id=".$_GET['id']);
	$rpt = $db->selectObject("formbuilder_report","form_id=".$_GET['id']);
	$items = $db->selectObjects("formbuilder_".$f->table_name);
	if (exponent_permissions_check("viewdata",unserialize($f->location_data))) {
		$columndef = "paginate.columns = new Array(";
		$columns = array();
		$sortfuncts = "";
		if ($rpt->column_names == '') {
			//define some default columns...
			$controls = $db->selectObjects("formbuilder_control","form_id=".$f->id." and is_readonly = 0 and is_static = 0");
//			if (!defined("SYS_SORTING")) include_once(BASE."subsystems/sorting.php");
			include_once(BASE."subsystems/sorting.php");
			usort($controls,"exponent_sorting_byRankAscending");
			
			foreach (array_slice($controls,0,5) as $control) {
				if ($rpt->column_names != '') $rpt->column_names .= '|!|';
				$rpt->column_names .= $control->name;
			}
		}
		
		foreach (explode("|!|",$rpt->column_names) as $column_name) {
			if ($column_name == "ip") {
				$columndef .= 'new cColumn("'.$i18n['ip'].'","ip",null,null),';
				$columns[$i18n['ip']] = 'ip';
			} elseif ($column_name == "user_id") {
				foreach ($items as $key=>$item) {
					if ($item->$column_name != 0) {
						 $locUser = exponent_users_getUserById($item->$column_name);
						 $item->$column_name = $locUser->username;
					} 
					else {
						$item->$column_name = '';
					}
					$items[$key] = $item;
				}
				$columndef .= 'new cColumn("'.$i18n['username'].'","user_id",null,null),';
				$columns[$i18n['username']] = 'user_id';
			} elseif ($column_name == "timestamp") {
				$srt = $column_name."_srt";
				foreach ($items as $key=>$item) {
					$item->$srt = $item->$column_name;
					$item->$column_name = strftime(DISPLAY_DATETIME_FORMAT,$item->$column_name);
					$items[$key] = $item;
				}
				$columndef .= 'new cColumn("'.$i18n['timestamp'].'","timestamp",null,f'.$srt.'),';
				$columns[$i18n['timestamp']] = 'timestamp';
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
		global $SYS_FLOW_REDIRECTIONPATH;
		$SYS_FLOW_REDIRECTIONPATH = "editfallback";
		$template->assign("backlink",exponent_flow_get());
		$template->register_permissions(array("administrate","editform","editformsettings","editreport","viewdata","editdata","deletedata"),unserialize($f->location_data));
		$SYS_FLOW_REDIRECTIONPATH = "exponent_default";
		$columndef .= 'new cColumn("Links","",links,null)';
		$columndef .= ');';
		
//		$template->assign('columdef',$columndef);
//		$template->assign('sortfuncs',$sortfuncts);
        $page = new expPaginator(array(
//                    'model'=>$f->table_name,
					'records'=>$items,
                    'where'=>1, 
                    'limit'=>10,
//                    'order'=>$order,
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