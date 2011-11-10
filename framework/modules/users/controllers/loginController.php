<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
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
/** @define "BASE" "../../../.." */

class loginController extends expController {
//    public $basemodel_name = 'user';
//    public $add_permissions = array(
//        'toggle_extension'=>'Activate Extensions',
//        'edituser'=>'Edit Users',
//        'kill_session'=>'End Sessions',
//        'boot_user'=>'Boot Users',
//    );
//    public $remove_permissions = array('create', 'edituser');
	public $codequality = 'beta';
	public $remove_configs = array(
        'aggregation',
        'comments',
		'ealerts',
        'files',
        'pagination',
        'rss',
        'tags'
    );

    public $useractions = array(
	    'showlogin'=>'Login',
    );

    function displayname() { return "Login Manager"; }
    function description() { return "This is the login management module. It allows for logging in, logging out, etc."; }

	/**
	 * Display a login view
	 */
	function showlogin() {
		global $db, $user, $order;

		$oicount = $order?$order->item_count:0;
		// FIGURE OUT IF WE"RE IN PREVIEW MODE OR NOT
		$level = 99;
		if (expSession::is_set('uilevel')) {
			$level = expSession::get('uilevel');
		}
		$previewtext = $level == UILEVEL_PREVIEW ? gt('Turn Preview Mode off') : gt('Turn Preview Mode on');
		$previewclass = $level == UILEVEL_PREVIEW ? 'preview_on' : 'preview_off';

		//eDebug($order);
		if (expSession::loggedIn() && $user->username != "anonymous") {
			$loggedin = 1;
			// Generate display name as username if the first and last name fields are blank.
			$display_name = $user->firstname . ' ' .$user->lastname;
			if (trim($display_name) == '') {
				$display_name = $user->username;
			}
			// Need to check for groups and whatnot
			if ($db->countObjects('groupmembership','member_id='.$user->id.' AND is_admin=1')) {
				$is_group_admin = 1;
			} else {
				$is_group_admin = 0;
			}
			assign_to_template(array('oicount'=>$oicount,'previewtext'=>$previewtext,'previewclass'=>$previewclass,'loggedin'=>$loggedin,'user'=>$user,'displayname'=>$display_name,'is_grou_admin'=>$is_group_admin));
		} else {
			//$template->assign('isecom',in_array('storeController',listActiveControllers()));
			$loggedin = 0;
			assign_to_template(array('oicount'=>$oicount,'previewtext'=>$previewtext,'previewclass'=>$previewclass,'loggedin'=>$loggedin,'user'=>$user));
		}
	}

	/**
	 * main logout method
	 */
	public static function logout() {
		expSession::logout();
		expSession::un_set("permissions");
		expSession::un_set('uilevel');
		expSession::clearCurrentUserSessionCache();
		flash('message', 'You have been logged out');
		redirect_to(array("section"=>SITE_DEFAULT_SECTION));
	}

	/**
	 * main login method
	 */
	public static function login() {
		user::login($_POST['username'],$_POST['password']);
		if (!isset($_SESSION[SYS_SESSION_KEY]['user'])) {
			flash('error', gt('Invalid Username / Password'));
			if (expSession::is_set('redirecturl_error')) {
				$url = expSession::get('redirecturl_error');
				expSession::un_set('redirecturl_error');
				header("Location: ".$url);
			} else {
				expHistory::back();
			}
		} else {
			global $user;
			if (isset($_POST['username'])) flash ('message', 'Welcome back '.$_POST['username']);
			foreach ($user->groups as $g) {
				if (!empty($g->redirect)) {
					$url = URL_FULL.$g->redirect;
					break;
				}
			}
			if (isset($url)) {
				header("Location: ".$url);
			} else {
				expHistory::back();
			}
		}
	}

	/**
	 * method to redirect to a login if needed
	 */
	public static function loginredirect() {
		global $user;
		global $router;

		ob_start();
		if ($user->isLoggedIn()) {
			header('Location: ' . expSession::get('redirecturl'));
		} else {
			//expSession::set('redirecturl', expHistory::getLastNotEditable());
			expSession::set('redirecturl', expHistory::getLast());
			expSession::set('redirecturl_error', makeLink(array('controller'=>'login', 'action'=>'loginredirect')));
//			expHistory::flowSet(SYS_FLOW_PUBLIC,SYS_FLOW_ACTION);
			expHistory::set('viewable', $router->params);
		}
		redirect_to(array('controller'=>'login', 'action'=>'showlogin'));
	}
}

?>
