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
	
if (!defined("EXPONENT")) exit("");

$i18n = exponent_lang_loadFile('modules/formbuilder/actions/view_data.php');

if (!defined('SYS_FORMS')) include_once(BASE.'subsystems/forms.php');
if (!defined('SYS_USERS')) include_once(BASE.'subsystems/users.php');
exponent_forms_initialize();

$template = new template('formbuilder','_data_view');
exponent_flow_set(SYS_FLOW_PUBLIC,SYS_FLOW_ACTION);

if (isset($_GET['id'])) {
	$_GET['id'] = intval($_GET['id']);
	
	$f = $db->selectObject("formbuilder_form","id=".$_GET['id']);
	$rpt = $db->selectObject("formbuilder_report","form_id=".$_GET['id']);
		
	$items = $db->selectObjects("formbuilder_".$f->table_name);
		
	if (exponent_permissions_check("viewdata",unserialize($f->location_data))) {
		$columndef = "paginate.columns = new Array(";
		$sortfuncts = "";
		if ($rpt->column_names == '') {
			//define some default columns...
			$controls = $db->selectObjects("formbuilder_control","form_id=".$f->id." and is_readonly = 0 and is_static = 0");
			if (!defined("SYS_SORTING")) include_once(BASE."subsystems/sorting.php");
			usort($controls,"exponent_sorting_byRankAscending");	
			
			foreach (array_slice($controls,0,5) as $control) {
				if ($rpt->column_names != '') $rpt->column_names .= '|!|';
				$rpt->column_names .= $control->name;
			}
		}
			
		foreach (explode("|!|",$rpt->column_names) as $column_name) {
			if ($column_name == "ip") {
				$columndef .= 'new cColumn("'.$i18n['ip'].'","ip",null,null),';
			} elseif ($column_name == "user_id") {
				foreach ($items as $key=>$item) {
					if ($item->$column_name != 0) {
						 $locUser = exponent_users_getUserById($item->$column_name);
						 $item->$column_name = $locUser->username;
					} else {
						$item->$column_name = '';
					}
					$items[$key] = $item;
				}
				$columndef .= 'new cColumn("'.$i18n['username'].'","user_id",null,null),';
			} elseif ($column_name == "timestamp") {
				$srt = $column_name."_srt";
				foreach ($items as $key=>$item) {
							
					$item->$srt = $item->$column_name;
					$item->$column_name = strftime(DISPLAY_DATETIME_FORMAT,$item->$column_name);
					$items[$key] = $item;
				}
				$columndef .= 'new cColumn("'.$i18n['timestamp'].'","timestamp",null,f'.$srt.'),';
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
							$item->$srt = $item->$column_name;
						}
						$item->$column_name = call_user_func(array($control_type,'templateFormat'),$item->$column_name,$ctl);
						$items[$key] = $item;
					}
					if (isset($datadef[DB_FIELD_TYPE]) && $datadef[DB_FIELD_TYPE] == DB_DEF_TIMESTAMP) {
						$columndef .= 'new cColumn("' . $control->caption . '","'.$column_name.'",null,f'.$srt.'),';
						$sortfuncts .= 'function f'.$srt.'(a,b) {return (a.var_'.$srt.'<b.var_'.$srt.')?1:-1;}';
					} else {
						$columndef .= 'new cColumn("' . $control->caption . '","'.$column_name.'",null,null),';
					}
				}
			}
		}
		
		/* Tyler's additions --
		Here I want to add a section that runs through items, which has the first row as headers, and creates a new array which it will then push out as a CSV download.
		*/
				
		if (LANG_CHARSET == 'UTF-8') {
			$file = chr(0xEF).chr(0xBB).chr(0xBF).$file;  // add utf-8 signature to file to open appropriately in Excel, etc...
		} else {
			$file = "";
		}

		$file .= sql2csv($items);

		//CODE FOR LATER CREAATING A TEMP FILE
		$tmpfname = tempnam(getcwd(), "rep"); // Rig

		$handle = fopen($tmpfname, "w");
		fwrite($handle,$file);
		fclose($handle);

		if(file_exists($tmpfname)) {

			ob_end_clean();

			// This code was lifted from phpMyAdmin, but this is Open Source, right?
			// 'application/octet-stream' is the registered IANA type but
			//        MSIE and Opera seems to prefer 'application/octetstream'
			// It seems that other headers I've added make IE prefer octet-stream again. - RAM

			$mime_type = (EXPONENT_USER_BROWSER == 'IE' || EXPONENT_USER_BROWSER == 'OPERA') ? 'application/octet-stream;' : 'text/comma-separated-values;';

			header('Content-Type: ' . $mime_type . ' charset=' . LANG_CHARSET. "'");
			header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			header("Content-length: ".filesize($tmpfname));
			header('Content-Transfer-Encoding: binary');
			header('Content-Encoding:');
			header('Content-Disposition: attachment; filename="' . 'report.csv' . '"');
			// IE need specific headers
			if (EXPONENT_USER_BROWSER == 'IE') {
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Vary: User-Agent');
			} else {
				header('Pragma: no-cache');
			}
			//Read the file out directly

			readfile($tmpfname);
			exit();

			unlink($tmpfname);

		} else {
			error_log("error file doesn't exist",0);
		}

	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

/**
This converts the sql statement into a nice CSV.
We grab the items array which is stored funkily in the DB in an associative array when we pull it.
So basically our aray looks like this:

ITEMS
{[id]=>myID, [Name]=>name, [Address]=>myaddr} 
{[id]=>myID1, [Name]=>name1, [Address]=>myaddr1} 
{[id]=>myID2, [Name]=>name2, [Address]=>myaddr2} 
{[id]=>myID3, [Name]=>name3, [Address]=>myaddr3} 
{[id]=>myID4, [Name]=>name4, [Address]=>myaddr4} 
{[id]=>myID5, [Name]=>name5, [Address]=>myaddr5} 

So by nature of the array, the keys are repetated in each line (id, name, etc)
So if we want to make a header row, we just run through once at the beginning and 
use the array_keys function to strip out a functional header
 * @param $items
 * @return string

 */

function sql2csv($items) {
	$str = "";
	foreach ($items as $key=>$item)  {
		if($str == "") {
			$header_Keys = array_keys((array)$item);
			foreach ($header_Keys as $individual_Header) {
				$str .= $individual_Header.",";
			}
			$str .= "\r\n";
		}
		foreach ($item as $bob=>$rowitem) {
		 	$rowitem = str_replace(",", " ", $rowitem);
			$str .= $rowitem.",";
		} //foreach rowitem
		$str = substr($str,0,strlen($str)-1);
		$str .= "\r\n";
	} //end of foreach loop
	return $str;
}

?>
