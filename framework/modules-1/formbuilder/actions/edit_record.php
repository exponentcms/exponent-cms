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

// Sanitize required _GET parameters
$_GET['id'] = intval($_GET['id']);
$_GET['form_id'] = intval($_GET['form_id']);

$f = $db->selectObject('formbuilder_form','id='.$_GET['form_id']);
$data = $db->selectObject('formbuilder_'.$f->table_name,'id='.$_GET['id']);
$controls = $db->selectObjects('formbuilder_control','form_id='.$_GET['form_id'],'rank');

if ($f && $data && $controls) {
	if (expPermissions::check('editdata',unserialize($f->location_data))) {
//		$controls = expSorter::sort(array('array'=>$controls,'sortby'=>'rank', 'order'=>'ASC'));

		$form = new form();
		foreach ($controls as $c) {
			$ctl = unserialize($c->data);
			$ctl->_id = $c->id;
			$ctl->_readonly = $c->is_readonly;
			if ($c->is_readonly == 0) {
				$name = $c->name;
				if ($c->is_static == 0) {
					$ctl->default = $data->$name;
				}
			}
			$form->register($c->name,$c->caption,$ctl);
		}
		
		$antispam = '';
		if (SITE_USE_ANTI_SPAM && ANTI_SPAM_CONTROL == 'recaptcha') {                
			// make sure we have the proper config.
			if (!defined('RECAPTCHA_PUB_KEY')) {
				$antispam .= '<h2 style="color:red">reCaptcha configuration is missing the public key.</h2>';
			}
			if ($user->isLoggedIn() && ANTI_SPAM_USERS_SKIP == 1) {
				// skip it for logged on users based on config
			} else {
				// include the library and show the form control
				require_once(BASE.'external/recaptchalib.php');
				$antispam .= recaptcha_get_html(RECAPTCHA_PUB_KEY);
				$antispam .= '<p>Fill out the above security question to submit your form.</p>';
			}
		}		
		
//		$form->register(uniqid(''),'', new htmlcontrol('<br /><br />'));
		$form->register(uniqid(''),'', new htmlcontrol($antispam));
		$form->register('submit','',new buttongroupcontrol(gt('Save'),'',gt('Cancel'),"",'editable'));
		$form->meta('action','submit_form');
		$form->meta('m',$loc->mod);
		$form->meta('s',$loc->src);
		$form->meta('isedit',1);
		$form->meta('i',$loc->int);
		$form->meta('id',$f->id);
		$form->meta('data_id',$data->id);
		$form->location($loc);
		
		$template = new template('formbuilder','_view_form');
		$template->assign('form_html',$form->toHTML($f->id));
		$template->assign('form',$f);
		$template->assign('edit_mode',1);
		$template->output();
	} else {
		echo SITE_403_HTML;
	}
} else {
	echo SITE_404_HTML;
}

?>
