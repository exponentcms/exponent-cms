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

if (!defined('EXPONENT')) exit('');

$container = null;
if (isset($_GET['id'])) {
	$container = $db->selectObject('container','id='.intval($_GET['id']));
}

if ($container != null) {
	$iloc = unserialize($container->internal);
	$cloc = unserialize($container->external);
	$cloc->int = $container->id;

	if (expPermissions::check('delete_module',$loc) || expPermissions::check('delete_module',$cloc) || expPermissions::check('administrate',$iloc)) {
		
//		container::delete($container,(isset($_GET['rerank']) ? 1 : 0));
		container::delete($container,(isset($_GET['rerank']) ? $_GET['rerank'] : 0));
		$db->delete('container','id='.$container->id);

        expSession::clearAllUsersSessionCache('containermodule');

		// Check to see if its the last reference
		$secref = $db->selectObject('sectionref',"module='".$iloc->mod."' AND source='".$iloc->src."' AND internal='".$iloc->int."'");
		if ($secref->refcount == 0 && expPermissions::check('administrate',$iloc) && call_user_func(array($iloc->mod,'hasContent')) == 1) {
			//FIXME: module/controller glue code
			// remove this controllers data from the search table.			
			if (expModules::controllerExists($iloc->mod)) {
			    $controller = new $iloc->mod($iloc->src);
			    $controller->delete_search();
			}

			expHistory::back();
            eDebug(expHistory::getLastNotEditable());
            // $template = new template('containermodule','_lastreferencedelete',$loc);
            // $template->assign('iloc',$iloc);
            // $template->assign('redirect',expHistory::getLastNotEditable());
            // $template->output();
		} else {
			expHistory::back();
		}
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>
