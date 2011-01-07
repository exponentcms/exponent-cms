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

function smarty_function_fileuploadcontrol($params,&$smarty) {
	global $db;
	$files = $db->selectObjects('file', 'item_id="'.$params['id'].'" AND item_type="'.$params['type'].'"');
	$template = new template('cermi','_main');	
	$template->assign('files', $files);
	$template->assign('item_type', $params['type']);
	$template->assign('item_id', $params['id']);
	$html = $template->render();
	echo $html;
}

?>
