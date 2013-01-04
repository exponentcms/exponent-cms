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

if (!defined('EXPONENT')) exit('');

if (expPermissions::check('configure',$loc)) {
	if (expTemplate::getModuleViewFile($loc->mod,'_configure',false) == TEMPLATE_FALLBACK_VIEW) {
//		$template = new template('common','_configure',$loc,false,'globalviews');
        $template = new template('common','_configure',$loc);
	} else {
		//$template = new template($loc->mod,'_configure',$loc);
//		$template = new template('common','_configure',$loc,false,'globalviews');
        $template = new template('common','_configure',$loc);
	}
	
	$hasConfig = 0;
	
	$submit = null;
	$form = null;
	
	if ($db->tableExists($_GET['module'].'_config') && class_exists($_GET['module'].'_config')) {
		$config = $db->selectObject($_GET['module'].'_config',"location_data='".serialize($loc)."'");	
		if (empty($config->location_data)) {
            $config = new stdClass();
            $config->location_data = serialize($loc);
        }
		$form = call_user_func(array($_GET['module'].'_config','form'),$config);
			
		if (isset($form->controls['submit'])) {
			$submit = $form->controls['submit'];
			$form->unregister('submit');
		}
		$hasConfig = 1; //We have a configuration stored in its own table
	}

	$container = $db->selectObject('container',"internal='".serialize($loc)."'");
	if ($container) {
		$values = ($container->view_data != '' ? unserialize($container->view_data) : array());
		$form = expTemplate::getViewConfigForm($loc->mod,$container->view,$form,$values);
		
		if (isset($form->controls['submit'])) { // Still have a submit button.
			$submit = $form->controls['submit'];
			$form->unregister('submit');
		}
		$hasConfig = 1; //We have a per-view, per-container configuration stored in the container data
	}
	//FIXME PLEASE EVALUATE: since expTemplate::getViewConfigForm is called only here, is it necessary to make it add
	//the submit button to the config form just to unregister and re-register it down here?

	if ($hasConfig) {
		$form->location($loc);
		$form->meta('action','saveconfig');
		$form->meta('_common','1');
	}
	
    $form->register(null,null,new htmlcontrol('<div class="loadingdiv">'.gt('Loading Module Configuration').'</div>'),true,'base');
	if ($submit !== null) {
		$form->register('submit','',$submit,true,'base');
	}

	if ($hasConfig) {
		$template->assign('form_html',$form->toHTML());
	}
	$template->assign('hasConfig',$hasConfig);
    $mod = new $loc->mod();
    $template->assign('title',$_GET['module'] != 'navigationController'? $mod->name() : '');

	$template->output();
} else {
	echo SITE_403_HTML;
}

?>
