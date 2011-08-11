<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Copyright (c) 2006 Maxim Mueller
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

$installed_editors = array();

foreach (glob(BASE . "themes/common/editors/*.tpl") as $installed_glue_file) {
   $installed_editor = basename($installed_glue_file, ".tpl");
   //also check if the editor is actually installed, not just its view file
   if (file_exists("external/editors/" . $installed_editor)) {
		$installed_editors[$installed_editor] = $installed_editor;
   }
}

$stuff = array(
	gt('General Site Configuration'),
	array(
		'ORGANIZATION_NAME'=>array(
            'title'=>gt('Organization Name'),
            'description'=>gt('The name of your company or organization.  This is used in several places in the system.'),
            'control'=>new textcontrol()
        ),
		'SITE_TITLE'=>array(
			'title'=>gt('Site Title'),
			'description'=>gt('The title of the website.'),
			'control'=>new textcontrol()
		),
		'SITE_HEADER'=>array(
			'title'=>gt('Site Header'),
			'description'=>gt('The header of the website.'),
			'control'=>new textcontrol()
		),
//		'USE_LANG'=>array(
//			'title'=>gt('Interface Language'),
//			'description'=>gt('What language should be used for the Exponent interface?'),
//			'control'=>new dropdowncontrol(0,exponent_lang_list())
//		),
		/*'SITE_ALLOW_REGISTRATION'=>array(
			'title'=>gt('Allow Registration?'),
			'description'=>gt('Whether or not new users should be allowed to create accounts for themselves.'),
			'control'=>new checkboxcontrol()
		),
		'SITE_USE_ANTI_SPAM'=>array(
			'title'=>gt('Use CAPTCHA Test?'),
			'description'=>gt('A CAPTCHA (Computer Automated Public Turing Test to Tell Computers and Humans Apart) is a means to prevent massive account registration.  When registering a new user account, the visitor will be required to enter a series of letters and numbers appearing in an image.  This prevents scripted bots from registering a large quantity of accounts.'),
			'control'=>new checkboxcontrol()
		),*/
		'SITE_KEYWORDS'=>array(
			'title'=>gt('Keywords'),
			'description'=>gt('Search engine keywords for the site.'),
			'control'=>new texteditorcontrol('',10,30)
		),
		'SITE_DESCRIPTION'=>array(
			'title'=>gt('Description'),
			'description'=>gt('A description of what the site is about.'),
			'control'=>new texteditorcontrol('',15,50)
		),
		'SITE_404_TITLE'=>array(
			'title'=>gt('Not Found Page Title'),
			'description'=>gt('Page title for your 404 Not Found Page'),
			'control'=>new textcontrol()
		),
		'SITE_404_HTML'=>array(
			'title'=>gt('"Not Found" Error Text'),
			'description'=>gt('HTML to show to a user when they try to request something that is not found (like a deleted post, section etc.)'),
			'control'=>new htmleditorcontrol('',15,50)
		),
		'SITE_403_REAL_HTML'=>array(
			'title'=>gt('"Access Denied" Error Text'),
			'description'=>gt('HTML to show to a user when they try to perform some action that their user account is not allowed to perform.'),
			'control'=>new htmleditorcontrol('',15,50)
		),
		'SITE_DEFAULT_SECTION'=>array(
			'title'=>gt('Default Section'),
			'description'=>gt('The default section.'),
			'control'=>new dropdowncontrol('',navigationmodule::levelDropDownControlArray(0))
		),
		'SITE_WYSIWYG_EDITOR'=>array(
			'title'=>gt('HTML Editor'),
			'description'=>gt('Choose the HTML editor to use as the default editor for this site.'),
			'control'=>new dropdowncontrol(null, $installed_editors)
		),
		'SESSION_TIMEOUT_ENABLE'=>array(
			'title'=>gt('Enable Session Timeout'),
			'description'=>gt('This will turn on and off the timeout value for users.'),
			'control'=>new checkboxcontrol()
		),
		'SESSION_TIMEOUT'=>array(
			'title'=>gt('Session Timeout'),
			'description'=>gt('How long a user can be idle (in seconds) before they are automatically logged out.'),
			'control'=>new textcontrol()
		),
		'SESSION_TIMEOUT_HTML'=>array(
			'title'=>gt('"Session Expired" Error Text'),
			'description'=>gt('HTML to show to a user when their session expires and they are trying to perform some action that requires them to have certain permissions.'),
			'control'=>new htmleditorcontrol('',15,50)
		),
		'FILE_DEFAULT_MODE_STR'=>array(
			'title'=>gt('Default File Permissions'),
			'description'=>gt('The readability / writability of uploaded files, for users other than the web server user.'),
			'control'=>new dropdowncontrol(null,exponent_config_dropdownData('file_permissions'))
		),
		'DIR_DEFAULT_MODE_STR'=>array(
			'title'=>gt('Default Directory Permissions'),
			'description'=>gt('The readability / writability of created directories, for users other than the web server user.'),
			'control'=>new dropdowncontrol(null,exponent_config_dropdownData('dir_permissions'))
		),
		'ENABLE_SSL'=>array(
			'title'=>gt('Enable SSL Support'),
			'description'=>gt('Whether or not to turn on Secure Linking through SSL'),
			'control'=>new checkboxcontrol()
		),
		'NONSSL_URL'=>array(
			'title'=>gt('Non-SSL URL Base'),
			'description'=>gt('Full URL of the website without SSL support (usually starting with "http://")'),
			'control'=>new textcontrol()
		),
		'SSL_URL'=>array(
			'title'=>gt('SSL URL Base'),
			'description'=>gt('Full URL of the website with SSL support (usually starting with "https://")'),
			'control'=>new textcontrol()
		),
//		'ENABLE_WORKFLOW'=>array(
//			'title'=>gt('Enable Workflow'),
//			'description'=>gt('Turns workflow on and off.  Leave off unless you are specifically using it, as workflow will effect system performance.'),
//			'control'=>new checkboxcontrol()
//		),
//		'WORKFLOW_REVISION_LIMIT'=>array(
//			'title'=>gt('Revision History Limit'),
//			'description'=>gt('The maximum number of major revisions (excluding the "current" revision) to keep per item of content.  A limit of 0 (zero) means that all revisions will be kept.'),
//			'control'=>new textcontrol()
//		),
		'HELP_URL'=>array(
			'title'=>gt('URL For Help Documentation'),
			'description'=>gt('This is where Exponent will look for help docs.  If unsure, leave the defaults unchanged.'),
			'control'=>new textcontrol()
		),
	)
);

return $stuff;

?>
