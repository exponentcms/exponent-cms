<?php

return array(
	'title'=>'General Site Configuration',

	'org_name'=>'Organization Name',
        'org_name_desc'=>'The name of your company or organization.  This is used in several places in the system.',

	'site_title'=>'Site Title',
	'site_title_desc'=>'The title of the website.',
	
	'site_header'=>'Site Header',
	'site_header_desc'=>'The header of the website.',
	
	'use_lang'=>'Interface Language',
	'use_lang_desc'=>'What language should be used for the Exponent interface?',

	'allow_registration'=>'Allow Registration?',
	'allow_registration_desc'=>'Whether or not new users should be allowed to create accounts for themselves.',

	'use_captcha'=>'Use CAPTCHA Test?',
	'use_captcha_desc'=>'A CAPTCHA (Computer Automated Public Turing Test to Tell Computers and Humans Apart) is a means to prevent massive account registration.  When registering a new user account, the visitor will be required to enter a series of letters and numbers appearing in an image.  This prevents scripted bots from registering a large quantity of accounts.',

	'site_keywords'=>'Keywords',
	'site_keywords_desc'=>'Search engine keywords for the site.',

	'site_description'=>'Description',
	'site_description_desc'=>'A description of what the site is about.',

	'site_404'=>'"Not Found" Error Text',
	'site_404_desc'=>'HTML to show to a user when they try to request something that is not found (like a deleted post, section etc.)',

	'site_403'=>'"Access Denied" Error Text',
	'site_403_desc'=>'HTML to show to a user when they try to perform some action that their user account is not allowed to perform.',

	'default_section'=>'Default Section',
	'default_section_desc'=>'The default section.',

	'enable_session_timeout'=>'Enable Session Timeout',
	'enable_session_timeout_desc'=>'This will turn on and off the timeout value for users.',

	'session_timeout'=>'Session Timeout',
	'session_timeout_desc'=>'How long a user can be idle (in seconds) before they are automatically logged out.',

	'timeout_error'=>'"Session Expired" Error Text',
	'timeout_error_desc'=>'HTML to show to a user when their session expires and they are trying to perform some action that requires them to have certain permissions.',

	'fileperms'=>'Default File Permissions',
	'fileperms_desc'=>'The readability / writability of uploaded files, for users other than the web server user.',

	'dirperms'=>'Default Directory Permissions',
	'dirperms_desc'=>'The readability / writability of created directories, for users other than the web server user.',

	'ssl'=>'Enable SSL Support',
	'ssl_desc'=>'Whether or not to turn on Secure Linking through SSL',

	'nonssl_url'=>'Non-SSL URL Base',
	'nonssl_url_desc'=>'Full URL of the website without SSL support (usually starting with "http://")',

	'ssl_url'=>'SSL URL Base',
	'ssl_url_desc'=>'Full URL of the website with SSL support (usually starting with "https://")',

	'revision_limit'=>'Revision History Limit',
	'revision_limit_desc'=>'The maximum number of major revisions (excluding the "current" revision) to keep per item of content.  A limit of 0 (zero) means that all revisions will be kept.',

	'enable_workflow'=>'Enable Workflow',
	'enable_workflow_desc'=>'Turns workflow on and off.  Leave off unless you are specifically using it, as workflow will effect system performance.',

	'wysiwyg_editor'=>'HTML Editor',
	'wysiwyg_editor_desc'=>'Choose the HTML editor to use as the default editor for this site.',
	
	'help_url'=>'URL For Help Documentation',
	'help_url_desc'=>'This is where Exponent will look for help docs.  If unsure, leave the defaults unchanged.',
);

?>
