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

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define("SYS_SEARCH",1);

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define("SEARCH_TYPE_ANY",1);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define("SEARCH_TYPE_ALL",2);
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
define("SEARCH_TYPE_PHRASE",3);

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_search_whereClause($fields,$terms,$modules,$type = SEARCH_TYPE_ANY) {
	$where = "(";

	$first_time = true;
	foreach ($fields as $field) {
		if ($first_time == false) {
			$where .= " OR ";
		} else {
			$first_time = false;
		}

		switch ($type) {
			case SEARCH_TYPE_ALL:
				$where .= "(" . $field . " LIKE '%" . implode("% ' AND $field LIKE ' %",$terms) . " %') ";
				break;
			case SEARCH_TYPE_PHRASE:
				$where .= $field . " LIKE '% " . implode(" ",$terms) . " %' ";
				break;
			default:
				$where .= $field . " LIKE '%" . implode("%' OR $field LIKE '%",$terms) . "%' ";
				break;
		}
	}

	$first_time = true;
	$where .= ') AND (';
	foreach ($modules as $mod) {
		if ($first_time == false) {
                        $where .= " OR ";
                } else {
                        $first_time = false;
                }

		$where .= 'ref_module="'.$mod.'"';
	}

	$where .= ')';
	
	return $where;
	//return substr($where,0,-4);
}
	
/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_search_saveSearchKey($search) {
	$search->title = " " . $search->title . " ";
	$search->body = " " . $search->body . " ";
	
	global $db;
	if (isset($search->id)) {
		$db->updateObject($search,"search");
	} else {
		$db->insertObject($search,"search");
	}
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_search_removeHTML($str) {
	$str = str_replace(array("\r\n","\n")," ",$str);
	return strip_tags(str_replace(array("<br/>","<br>","<br />","</div>"),"\n",$str));
}

/* exdoc
 * @state <b>UNDOCUMENTED</b>
 * @node Undocumented
 */
function exponent_search_cleanSearchQuery($query) {
	$exclude = array_map("trim",file(BASE."subsystems/search/exclude.en.list"));
	$newquery = array('valid'=>array(),'excluded'=>array());
	foreach ($query as $q_tok) {
		if (!in_array($q_tok,$exclude)) {
			$newquery['valid'][] = $q_tok;
		} else {
			$newquery['excluded'][] = $q_tok;
		}
	}
	return $newquery;
}

function getModuleNames($mods) {
	$mod_list = array();

	if(!isset($mods) || $mods == null) {
		$mods = exponent_modules_list();
	}
	
        foreach ($mods as $mod) {
        	$name = null;
        	if (class_exists($mod) && is_callable(array($mod,'searchName'))) {
        		$name = call_user_func(array($mod,'searchName'));
	        } elseif (class_exists($mod) && is_callable(array($mod,'spiderContent'))) {
        		if (call_user_func(array($mod,'spiderContent'))) {
                		$name = $mod;
                	}
        	}

        	if ($name != null) {
        		$mod_list[$mod] = $name;
        	}
        }
        
	return $mod_list;
}

?>
