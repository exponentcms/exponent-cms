{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
 *
 * This file is part of Exponent
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * GPL: http://www.gnu.org/licenses/gpl.txt
 *
 *}

{uniqueid assign="config"}

{messagequeue}

<div id="siteconfig" class="module administration configure-site bootstrap3">
	<div class="form_header">
		<div class="info-header">
			<div class="related-actions">
			    {help text="Get Help with"|gettext|cat:" "|cat:("configuring your website"|gettext) page="site-configuration"}
			</div>
			<h1>{'Configure Website'|gettext}</h1>
		</div>
	</div>
    {form controller="administration" action=update_siteconfig}
        <div id="{$config}" class="">
            <ul class="nav nav-tabs" role="tablist">
	            <li role="presentation" class="active"><a href="#tab1" role="tab" data-toggle="tab"><em>{"General"|gettext}</em></a></li>
	            <li role="presentation"><a href="#tab2" role="tab" data-toggle="tab"><em>{"Anti-Spam"|gettext}</em></a></li>
	            <li role="presentation"><a href="#tab3" role="tab" data-toggle="tab"><em>{"User Registration"|gettext}</em></a></li>
	            <li role="presentation"><a href="#tab4" role="tab" data-toggle="tab"><em>{"Comment Policies"|gettext}</em></a></li>
	            <li role="presentation"><a href="#tab5" role="tab" data-toggle="tab"><em>{"Display"|gettext}</em></a></li>
                <li role="presentation"><a href="#tab6" role="tab" data-toggle="tab"><em>{"File Manager"|gettext}</em></a></li>
	            {if $user->isSuperAdmin()}
					<li role="presentation"><a href="#tab7" role="tab" data-toggle="tab"><em>{"Mail Server"|gettext}</em></a></li>
		            <li role="presentation"><a href="#tab8" role="tab" data-toggle="tab"><em>{"Maintenance"|gettext}</em></a></li>
		            <li role="presentation"><a href="#tab9" role="tab" data-toggle="tab"><em>{"Security"|gettext}</em></a></li>
					<li role="presentation"><a href="#tab10" role="tab" data-toggle="tab"><em>{"Help Links"|gettext}</em></a></li>
					<li role="presentation"><a href="#tab11" role="tab" data-toggle="tab"><em>{"WYSIWYG Editor"|gettext}</em></a></li>
		            <li role="presentation"><a href="#tab12" role="tab" data-toggle="tab"><em>{"Error Messages"|gettext}</em></a></li>
		            <li role="presentation"><a href="#tab13" role="tab" data-toggle="tab"><em>{"PDF Generation"|gettext}</em></a></li>
					<li role="presentation"><a href="#tab14" role="tab" data-toggle="tab"><em>{"Minify"|gettext}</em></a></li>
					<li role="presentation"><a href="#tab15" role="tab" data-toggle="tab"><em>{"Search Report"|gettext}</em></a></li>
                    <li role="presentation"><a href="#tab16" role="tab" data-toggle="tab"><em>{"e-Commerce"|gettext}</em></a></li>
                    <li role="presentation"><a href="#tab17" role="tab" data-toggle="tab"><em>{"Profiles"|gettext}</em></a></li>
	            {/if}
            </ul>
            <div class="tab-content">
                <div id="tab1" role="tabpanel" class="tab-pane fade in active">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("general site configuration settings"|gettext) module="general-configuration"}
                        </div>
		                <h2>{"General Site Configuration"|gettext}</h2>
                    </div>
                    {control type="text" name="sc[ORGANIZATION_NAME]" label="Site/Organization Name"|gettext value=$smarty.const.ORGANIZATION_NAME focus=1}
                    {control type="text" name="sc[SITE_TITLE]" label="Site Title"|gettext value=$smarty.const.SITE_TITLE}
					{control type="text" name="sc[SITE_HEADER]" label="Site Header"|gettext value=$smarty.const.SITE_HEADER}
                    {control type="checkbox" postfalse=1 name="sc[SEF_URLS]" label="Search Engine Friendly URLs?"|gettext checked=$smarty.const.SEF_URLS value=1}
					{control type="checkbox" postfalse=1 name="sc[ADVERTISE_RSS]" label="Advertise RSS Feeds to Web Browsers?"|gettext checked=$smarty.const.ADVERTISE_RSS value=1}
                    {control type="checkbox" postfalse=1 name="sc[SKIP_VERSION_CHECK]" label="Skip Automatic Online Version Update Check?"|gettext checked=$smarty.const.SKIP_VERSION_CHECK value=1 description='You can still check for an updated version using the Exponent, Super-Admin Tools menu'|gettext}
                    {control type="dropdown" name="sc[SITE_DEFAULT_SECTION]" label="Default Section (Home Page)"|gettext items=$section_dropdown default=$smarty.const.SITE_DEFAULT_SECTION}
                    {control type="textarea" name="sc[SITE_KEYWORDS]" label='Meta Keywords'|gettext value=$smarty.const.SITE_KEYWORDS description='Comma separated phrases'|gettext}
	                {control type="textarea" name="sc[SITE_DESCRIPTION]" label='Meta Description'|gettext value=$smarty.const.SITE_DESCRIPTION}
                </div>
                <div id="tab2" role="tabpanel" class="tab-pane fade">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("anti-spam measure settings"|gettext) module="anti-spam-measures"}
                        </div>
		                <h2>{"Anti-Spam Measures"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[SITE_USE_ANTI_SPAM]" id=use_antispam label="Use Anti-Spam measures?"|gettext checked=$smarty.const.SITE_USE_ANTI_SPAM value=1}
                    <span id="antispam">
                    {control type="checkbox" postfalse=1 name="sc[ANTI_SPAM_USERS_SKIP]" label="Skip using Anti-Spam measures for Logged-In Users?"|gettext checked=$smarty.const.ANTI_SPAM_USERS_SKIP value=1}
                    {control type="dropdown" name="sc[ANTI_SPAM_CONTROL]" label="Anti-Spam Method"|gettext items=$as_types default=$smarty.const.ANTI_SPAM_CONTROL}
                    {group label="reCAPTCHA Settings"|gettext}
                        <blockquote>
                        {'To obtain the reCAPTCHA \'keys\', you\'ll need to first have a'|gettext} <a href="http://www.google.com/" target="_blank">{"Google account"|gettext}</a> {"to log in, then setup up a reCAPTCHA account for your domain(s)"|gettext} <a href="http://www.google.com/recaptcha/admin" target="_blank">{"here"|gettext}</a>
                        </blockquote>
                        {control type="dropdown" name="sc[RECAPTCHA_THEME]" label="reCaptcha Theme"|gettext items=$as_themes default=$smarty.const.RECAPTCHA_THEME}
                        {control type="text" name="sc[RECAPTCHA_PUB_KEY]" label="reCAPTCHA Site Key"|gettext value=$smarty.const.RECAPTCHA_PUB_KEY}
                        {control type="text" name="sc[RECAPTCHA_PRIVATE_KEY]" label="reCAPTCHA Secret Key"|gettext value=$smarty.const.RECAPTCHA_PRIVATE_KEY}
                    {/group}
                    </span>
                </div>
                <div id="tab3" role="tabpanel" class="tab-pane fade">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("user registration settings"|gettext) module="user-registration"}
                        </div>
		                <h2>{"User Registration"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[SITE_ALLOW_REGISTRATION]" label="Allow users to create accounts for themselves"|gettext checked=$smarty.const.SITE_ALLOW_REGISTRATION value=1}
                    {control type="checkbox" postfalse=1 name="sc[USER_REGISTRATION_USE_EMAIL]" label="Use an email address instead of a username"|gettext checked=$smarty.const.USER_REGISTRATION_USE_EMAIL value=1}
                    {control type="checkbox" postfalse=1 name="sc[USER_NO_PASSWORD_CHANGE]" label="Disable User Request Password Change Feature"|gettext checked=$smarty.const.USER_NO_PASSWORD_CHANGE value=1}
                    {group label="New User Notification Email"|gettext}
                        {control type="checkbox" postfalse=1 name="sc[USER_REGISTRATION_SEND_NOTIF]" label="Notify a site administrator when a new user registers on your website"|gettext checked=$smarty.const.USER_REGISTRATION_SEND_NOTIF value=1}
                        {control type="text" name="sc[USER_REGISTRATION_NOTIF_SUBJECT]" label='Subject of the administrator\'s new user notification'|gettext value=$smarty.const.USER_REGISTRATION_NOTIF_SUBJECT}
                        {control type=email name="sc[USER_REGISTRATION_ADMIN_EMAIL]" label="Email address of administrator that should be notified when a user signs up"|gettext value=$smarty.const.USER_REGISTRATION_ADMIN_EMAIL}
                    {/group}
                    {group label="New User Welcome Message"|gettext}
                        {control type="checkbox" postfalse=1 name="sc[USER_REGISTRATION_SEND_WELCOME]" label="Send an email to the user after registering?"|gettext checked=$smarty.const.USER_REGISTRATION_SEND_WELCOME value=1}
                        {control type="text" name="sc[USER_REGISTRATION_WELCOME_SUBJECT]" label="Welcome Email Subject"|gettext value=$smarty.const.USER_REGISTRATION_WELCOME_SUBJECT}
                        {control type="textarea" name="sc[USER_REGISTRATION_WELCOME_MSG]" label="Welcome Email Content"|gettext value=$smarty.const.USER_REGISTRATION_WELCOME_MSG}
                    {/group}
                    {if function_exists('ldap_connect')}
                    {group label="LDAP Authentication"|gettext}
                        {control type="checkbox" postfalse=1 name="sc[USE_LDAP]" id=use_ldap label="Turn on LDAP Authentication?"|gettext checked=$smarty.const.USE_LDAP value=1 description='Checking this option will cause Exponent to try to authenticate to the ldap server listed below.'|gettext}
                        <span id="ldap">
                        {control type="text" name="sc[LDAP_SERVER]" label="LDAP Server"|gettext value=$smarty.const.LDAP_SERVER description='Enter the hostname or IP of the LDAP server.'|gettext}
                        {control type="text" name="sc[LDAP_BASE_CONTEXT]" label="Base Context"|gettext value=$smarty.const.LDAP_BASE_CONTEXT description='Enter the Base Context for this LDAP connection. (e.g., ou=users, dc=mycompanysite, dc=local)'|gettext}
                        {control type="text" name="sc[LDAP_BASE_DN]" label="Base Domain"|gettext value=$smarty.const.LDAP_BASE_DN description='Enter the Base Domain for this LDAP connection. (e.g., mycompanysite.local)'|gettext}
                        {control type="text" name="sc[LDAP_BIND_USER]" label="LDAP Bind User"|gettext value=$smarty.const.LDAP_BIND_USER description='The username or context for the binding to the LDAP Server to perform administration tasks.'|gettext}
                        {control type="password" name="sc[LDAP_BIND_PASS]" label="LDAP Bind Password"|gettext value=$smarty.const.LDAP_BIND_PASS description='Enter the password for the username/context listed above.'|gettext}
                        </span>
                    {/group}
                    {/if}
                </div>
                <div id="tab4" role="tabpanel" class="tab-pane fade">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("user comment policy settings"|gettext) module="user-comment-policies"}
                        </div>
		                <h2>{"User Comment Policies"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[COMMENTS_REQUIRE_LOGIN]" label="Require User Login to Post Comments?"|gettext checked=$smarty.const.COMMENTS_REQUIRE_LOGIN value=1}
                    {control type="checkbox" postfalse=1 name="sc[COMMENTS_REQUIRE_APPROVAL]" label="All Comments Must be Approved?"|gettext checked=$smarty.const.COMMENTS_REQUIRE_APPROVAL value=1}
                    {group label="New Comment Notification Email"|gettext}
                        {control type="checkbox" postfalse=1 name="sc[COMMENTS_REQUIRE_NOTIFICATION]" label="Notify a site administrator of New Comments?"|gettext checked=$smarty.const.COMMENTS_REQUIRE_NOTIFICATION value=1}
                        {*{control type=text multiple="1" name="sc[COMMENTS_NOTIFICATION_EMAIL]" label="Email address(es) that should be notified of New Comments"|gettext description="Enter multiple addresses by using a comma to separate them"|gettext value=$smarty.const.COMMENTS_NOTIFICATION_EMAIL}*}
                    {control type=email multiple="1" name="sc[COMMENTS_NOTIFICATION_EMAIL]" label="Email address(es) that should be notified of New Comments"|gettext description="Enter multiple addresses by using a comma to separate them"|gettext value=$smarty.const.COMMENTS_NOTIFICATION_EMAIL}
                    {/group}
                </div>
                <div id="tab5" role="tabpanel" class="tab-pane fade">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("display settings"|gettext) module="display-settings"}
                        </div>
		                <h2>{"Display Settings"|gettext}</h2>
                    </div>
                    {control type="dropdown" name="sc[LANGUAGE]" label="Display Language"|gettext items=$langs default=$smarty.const.LANGUAGE}
                    {*{control type="dropdown" name="sc[DISPLAY_THEME_REAL]" label="Theme <a href=\"manage_themes\">(More Theme Options)</a>"|gettext items=$themes default=$smarty.const.DISPLAY_THEME_REAL}*}
	                {*<h3><a href="manage_themes">{'Display Theme Options'|gettext}</a></h3>*}
                    {*{$link = makeLink($manage_themes)}*}
                    {*{icon button=true link=$link text='Display Theme Options'|gettext}*}
	                {control type="checkbox" postfalse=1 name="sc[INVERT_HIDE_TITLE]" label="Reverse the Logic of Hide Module Title setting?"|gettext checked=$smarty.const.INVERT_HIDE_TITLE value=1 description='Changes default of always show title to always hide title, unless module setting is checked.'|gettext}
                    {control type="checkbox" postfalse=1 name="sc[FORCE_MOBILE]" label="Force Display of the Mobile Theme Variation (if available)?"|gettext checked=$smarty.const.FORCE_MOBILE value=1}
                    {group label="Display Formats"|gettext}
                        {control type="dropdown" name="sc[DISPLAY_ATTRIBUTION]" label="Attribution Display"|gettext items=$attribution default=$smarty.const.DISPLAY_ATTRIBUTION}
                        {control type="dropdown" name="sc[DISPLAY_DATETIME_FORMAT]" label="Date/Time Format"|gettext items=$datetime_format default=$smarty.const.DISPLAY_DATETIME_FORMAT}
                        {control type="dropdown" name="sc[DISPLAY_DATE_FORMAT]" label="Date Format"|gettext items=$date_format default=$smarty.const.DISPLAY_DATE_FORMAT}
                        {control type="dropdown" name="sc[DISPLAY_TIME_FORMAT]" label="Time Format"|gettext items=$time_format default=$smarty.const.DISPLAY_TIME_FORMAT}
                        {control type="dropdown" name="sc[DISPLAY_START_OF_WEEK]" label="Start of Week"|gettext items=$start_of_week default=$smarty.const.DISPLAY_START_OF_WEEK}
                    {/group}
	                {control type="dropdown" name="sc[DISPLAY_DEFAULT_TIMEZONE]" label="Default time zone for this site"|gettext|cat:(' <br />'|cat:("CAUTION: Changes may affect calendars and other features using date functions."|gettext)) items=$timezones default=$smarty.const.DISPLAY_DEFAULT_TIMEZONE}
                    {control type="radiogroup" name="sc[SLINGBAR_TOP]" label="Default Admin Slingbar Position" items="Top of Viewport,Bottom of Viewport"|gettxtlist values="1,0" default=$smarty.const.SLINGBAR_TOP}
					{control type="text" name="sc[THUMB_QUALITY]" label="Thumbnail JPEG Quality"|gettext|cat:" (0 - 95)" value=$smarty.const.THUMB_QUALITY|default:75 size="2"}
                    {control type="checkbox" name="sc[AJAX_PAGING]" label="Use ajax paging if available"|gettext value=1 checked=$smarty.const.AJAX_PAGING description='Can decrease paging loading time'|gettext}
                </div>
                <div id="tab6" role="tabpanel" class="tab-pane fade">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("file manager settings"|gettext) module="filemanager-settings"}
                        </div>
		                <h2>{"File Manager/Uploader Settings"|gettext}</h2>
                    </div>
                    {control type="dropdown" name="sc[SITE_FILE_MANAGER]" label="File Manager"|gettext items="Traditional,elFinder"|gettxtlist values="picker,elfinder" default=$smarty.const.SITE_FILE_MANAGER}
                    {control type="dropdown" name="sc[ELFINDER_THEME]" label="elFinder Theme"|gettext items=$elf_themes default=$smarty.const.ELFINDER_THEME}
                    {control type="text" name="sc[FM_WIDTH]" label="Popup Window Width"|gettext value=$smarty.const.FM_WIDTH|default:1024 size="4"}
                    {control type="text" name="sc[FM_HEIGHT]" label="Popup Window Height"|gettext value=$smarty.const.FM_HEIGHT|default:600 size="4"}
                    {control type="text" name="sc[FM_LIMIT]" label="Number of Files per Page"|gettext value=$smarty.const.FM_LIMIT|default:25 size="4"}
                    {control type="text" name="sc[FM_SIMLIMIT]" label="Number of Simultaneous Uploads"|gettext value=$smarty.const.FM_SIMLIMIT|default:3 size="2"}
                    {control type="checkbox" postfalse=1 name="sc[FM_THUMBNAILS]" label="Show Image Thumbnails?"|gettext checked=$smarty.const.FM_THUMBNAILS value=1}
                    {control type="text" name="sc[FM_THUMB_SIZE]" label="Thumbnail Size"|gettext value=$smarty.const.FM_THUMB_SIZE|default:48 size="4"}
                    {control type="text" name="sc[UPLOAD_WIDTH]" label="Uploader Default Max Width/Height to Downsize Graphics"|gettext value=$smarty.const.UPLOAD_WIDTH|default:400 size="4"}
                    {group label="Quick Add Settings"|gettext}
                        {control type="text" name="sc[QUICK_UPLOAD_WIDTH]" label="Force Quick Add to Downsize Graphics to Max Width/Height"|gettext value=$smarty.const.QUICK_UPLOAD_WIDTH|default:0 size="4" description='Zero or Empty means do NOT resize on a Quick Add Upload'|gettext}
                        {if $smarty.const.SITE_FILE_MANAGER == 'elfinder'}
                            {control type="text" name="sc[QUICK_UPLOAD_FOLDER]" label="Quick Add Upload Subfolder"|gettext value=$smarty.const.QUICK_UPLOAD_FOLDER}
                        {else}
                            {control type=dropdown name="sc[QUICK_UPLOAD_FOLDER]" label="Select the Quick Add Upload Folder"|gettext items=$folders value=$smarty.const.QUICK_UPLOAD_FOLDER}
                        {/if}
                    {/group}
                </div>
                {if $user->isSuperAdmin()}
                <div id="tab7" role="tabpanel" class="tab-pane fade">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("mail server settings"|gettext) module="mail-server-settings"}
                        </div>
		                <h2>{"Mail Server Settings"|gettext}</h2>
                    </div>
                    {control type=email name="sc[SMTP_FROMADDRESS]" label="From Address"|gettext value=$smarty.const.SMTP_FROMADDRESS description='This MUST be in a valid email address format or sending mail may fail!'|gettext}
                    {control type="checkbox" postfalse=1 name="sc[SMTP_USE_PHP_MAIL]" id=no_smtp label='Use simplified php mail() function instead of SMTP?'|gettext checked=$smarty.const.SMTP_USE_PHP_MAIL value=1}
	                <span id="smtp">
                    ({"or"|gettext})
                    {group label="SMTP Server Settings"|gettext}
                        {control type="text" name="sc[SMTP_SERVER]" label="SMTP Server"|gettext value=$smarty.const.SMTP_SERVER}
                        {control type="text" name="sc[SMTP_PORT]" label="SMTP Port"|gettext value=$smarty.const.SMTP_PORT}
                        {control type="dropdown" name="sc[SMTP_PROTOCOL]" label="Type of Encrypted Connection"|gettext items=$protocol default=$smarty.const.SMTP_PROTOCOL includeblank="None"}
                        {control type="text" name="sc[SMTP_USERNAME]" label="SMTP Username"|gettext value=$smarty.const.SMTP_USERNAME}
                        {control type="password" name="sc[SMTP_PASSWORD]" label="SMTP Password"|gettext value=$smarty.const.SMTP_PASSWORD}
                        {control type="checkbox" postfalse=1 name="sc[SMTP_DEBUGGING]" label="Turn On SMTP Debugging?"|gettext checked=$smarty.const.SMTP_DEBUGGING value=1}
                    {/group}
                    </span>
                </div>
                <div id="tab8" role="tabpanel" class="tab-pane fade">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("site maintenance mode settings"|gettext) module="site-maintenance-mode-settings"}
                        </div>
		                <h2>{"Site Maintenance Mode Settings"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[MAINTENANCE_MODE]" label="Place Site in Maintenance Mode?"|gettext checked=$smarty.const.MAINTENANCE_MODE value=1}
                    {control type="html" name="sc[MAINTENANCE_MSG_HTML]" label="Maintenance Mode Message"|gettext value=$smarty.const.MAINTENANCE_MSG_HTML}
                    {control type="checkbox" postfalse=1 name="sc[MAINTENANCE_USE_RETURN_TIME]" label="Display a countdown clock until site returns?"|gettext checked=$smarty.const.MAINTENANCE_USE_RETURN_TIME value=1}
                    {group label="Maintenance Countdown Settings"|gettext}
                        {control type="text" name="sc[MAINTENANCE_RETURN_TEXT]" label="Site will return message"|gettext value=$smarty.const.MAINTENANCE_RETURN_TEXT}
                        {control type="yuicalendar" name="sc[MAINTENANCE_RETURN_TIME]" label="Site will return time"|gettext value=$smarty.const.MAINTENANCE_RETURN_TIME}
                    {/group}
                </div>
                <div id="tab9" role="tabpanel" class="tab-pane fade">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("security settings"|gettext) module="security-settings"}
                        </div>
		                <h2>{"Security Settings"|gettext}</h2>
                    </div>
                    {group label='Account Password Strength'|gettext}
                        {control type="number" name="sc[NEW_PASSWORD]" label="Password Crypto Depth"|gettext min=0 value=$smarty.const.NEW_PASSWORD|default:0 description='Enter \'0\' to use old md5 method'|gettext}
                        {control type="number" name="sc[MIN_PWD_LEN]" label="Minimum Password Length"|gettext min=6 value=$smarty.const.MIN_PWD_LEN|default:8}
                        {control type="number" name="sc[MIN_UPPER]" label="Password Uppercase Letters Required"|gettext min=0 value=$smarty.const.MIN_UPPER|default:0 description='Must new passwords include upper case letters?'|gettext}
                        {control type="number" name="sc[MIN_DIGITS]" label="Password Digits Required"|gettext min=0 value=$smarty.const.MIN_DIGITS|default:0 description='Must new passwords include numeric characters?'|gettext}
                        {control type="number" name="sc[MIN_SYMBOL]" label="Password Symbols Required"|gettext min=0 value=$smarty.const.MIN_SYMBOL|default:0 description='Must new passwords include symbols?'|gettext}
                    {/group}
                    {group label='Session Timeout'|gettext}
                        {control type="checkbox" postfalse=1 name="sc[SESSION_TIMEOUT_ENABLE]" label="Enable Session Timeout?"|gettext checked=$smarty.const.SESSION_TIMEOUT_ENABLE value=1}
                        {control type="text" name="sc[SESSION_TIMEOUT]" label="Session Timeout in seconds"|gettext value=$smarty.const.SESSION_TIMEOUT}
                    {/group}
                    {control type="dropdown" name="sc[FILE_DEFAULT_MODE_STR]" label="Default File Permissions"|gettext items=$file_permisions default=$smarty.const.FILE_DEFAULT_MODE_STR}
                    {control type="dropdown" name="sc[DIR_DEFAULT_MODE_STR]" label="Default Directory Permissions"|gettext items=$dir_permissions default=$smarty.const.DIR_DEFAULT_MODE_STR}
                    {control type="checkbox" postfalse=1 name="sc[ENABLE_SSL]" label="Enable SSL (https://) Support?"|gettext checked=$smarty.const.ENABLE_SSL value=1}
                    {*{control type="text" name="sc[NONSSL_URL]" label="Non-SSL URL Base"|gettext value=$smarty.const.NONSSL_URL}*}
                    {*{control type="text" name="sc[SSL_URL]" label="SSL URL Base"|gettext value=$smarty.const.SSL_URL}*}
                    {control type="checkbox" postfalse=1 name="sc[DISABLE_PRIVACY]" label="Disable Privacy Check?"|gettext checked=$smarty.const.DISABLE_PRIVACY value=1 description='Exponent protects private page and module content; but this can prevent display of content in some scenarios'|gettext}
                    {group label='XMLRPC'}
                        {control type="checkbox" postfalse=1 name="sc[USE_XMLRPC]" label="Activate Remote Blog Editing?"|gettext checked=$smarty.const.USE_XMLRPC value=1 description='Allows access to xmlrpc.php to create and edit blog posts on an external application'|gettext}
                        {control type="checkbox" postfalse=1 name="sc[NO_XMLRPC_DESC]" label="MS Word Remote Blog Editing Fix?"|gettext checked=$smarty.const.NO_XMLRPC_DESC value=1 description='MS Word won\'t display recent posts list if it\'s too long, so we truncate the descriptions'|gettext}
                    {/group}
                </div>
                <div id="tab10" role="tabpanel" class="tab-pane fade">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("help link settings"|gettext) module="help-link-settings"}
                        </div>
		                <h2>{"Help Link Settings"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[HELP_ACTIVE]" label="Enable Help links to online documentation?"|gettext checked=$smarty.const.HELP_ACTIVE value=1}
                    {control type=url name="sc[HELP_URL]" label="URL for Help Documentation"|gettext value=$smarty.const.HELP_URL}
                </div>
                <div id="tab11" role="tabpanel" class="tab-pane fade">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("WYSIWYG Editor Settings"|gettext) module="wysiwyg-editor-settings"}
                        </div>
		                <h2>{"WYSIWYG Editor Settings"|gettext}</h2>
                    </div>
                    {$paramc = ["editor" => "ckeditor"]}
                    {$paramt = ["editor" => "tinymce"]}
                    <div id="alt-control-wysiwyg" class="alt-control">
                        <div class="control"><label class="label">{'WYSIWYG Editor'|gettext}</label></div>
                        <div class="alt-body">
                            {control type=radiogroup columns=2 name="sc[SITE_WYSIWYG_EDITOR]" items="CKEditor,TinyMCE"|gettxtlist values="ckeditor,tinymce" default=$smarty.const.SITE_WYSIWYG_EDITOR|default:"ckeditor"}
                            <div id="ckeditor-div" class="alt-item" style="display:none;">
                                {showmodule controller=expHTMLEditor action=manage params=$paramc}
                            </div>
                            <div id="tinymce-div" class="alt-item" style="display:none;">
                                {showmodule controller=expHTMLEditor action=manage params=$paramt}
                            </div>
                        </div>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[EDITOR_FAST_SAVE]" label="Always Save Inline Editing Changes w/o Prompt?"|gettext checked=$smarty.const.EDITOR_FAST_SAVE value=1}
                </div>
                <div id="tab12" role="tabpanel" class="tab-pane fade">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("error message settings"|gettext) module="error-messages"}
                        </div>
		                <h2>{"Error Messages"|gettext}</h2>
                    </div>
                    {control type="text" name="sc[SITE_404_TITLE]" label='Page Title For \'Not Found\' (404) Error'|gettext value=$smarty.const.SITE_404_TITLE}
                    {control type="html" name="sc[SITE_404_HTML]" label='\'Not Found\' (404) Error Message'|gettext value=$smarty.const.SITE_404_HTML}
                    {control type="text" name="sc[SITE_404_FILE]" label='Server Default Page For \'Not Found\' (404) Error'|gettext value=$smarty.const.SITE_404_FILE description='If your server sends 404 errors to a default page, enter it here (missing.html, etc...)'|gettext}
                    {control type="html" name="sc[SITE_403_REAL_HTML]" label='\'Access Denied\' (403) Error Message'|gettext value=$smarty.const.SITE_403_REAL_HTML}
                    {control type="text" name="sc[SITE_403_FILE]" label='Server Default Page For \'Access Denied\' (403) Error'|gettext value=$smarty.const.SITE_403_FILE description='If your server sends 403 errors to a default page, enter it here (forbidden.html, etc...)'|gettext}
                    {control type="text" name="sc[SITE_500_FILE]" label='Server Default Page For \'Server Internal Error\' (500) Error'|gettext value=$smarty.const.SITE_500_FILE description='If your server sends 500 errors to a default page, enter it here (internal_error.html, etc...)'|gettext}
                    {control type="html" name="sc[SESSION_TIMEOUT_HTML]" label='\'Session Expired\' Error  Message'|gettext value=$smarty.const.SESSION_TIMEOUT_HTML}
                </div>
                <div id="tab13" role="tabpanel" class="tab-pane fade">
                    <div class="info-header">
                       <div class="related-actions">
                        {help text="Get Help with"|gettext|cat:" "|cat:("generating PDF settings"|gettext) module="pdf-generation"}
                       </div>
                    <h2>{"PDF Generation"|gettext}</h2>
                   </div>
                   <div id="alt-control-pdf" class="alt-control">
                       <div class="control"><label class="label">{'PDF Generation Engine'|gettext}</label></div>
                       <div class="alt-body">
                           {control type=radiogroup columns=4 name="sc[HTMLTOPDF_ENGINE]" items="None,mPDF v5,mPDF v6,dompdf v0.6,HTML2PDF,WKHTMLtoPDF"|gettxtlist values="none,expMPDF,expMPDF6,expDOMPDF,expHTML2PDF,expWKPDF" default=$smarty.const.HTMLTOPDF_ENGINE|default:"none"}
                           <div id="none-div" class="alt-item" style="display:none;">
                               <blockquote>
                               {'Export as PDF will be unavailable since there is no PDF Generation Engine installed and configured.'|gettext}
                               </blockquote>
                           </div>
                           <div id="expMPDF-div" class="alt-item" style="display:none;">
                               {if !file_exists("`$smarty.const.BASE`external/MPDF57/mpdf.php")}
                                   <div style="color:#ff0000;font-weight:bold;">
                                       {'mPDF v5 is NOT installed!'|gettext}
                                   </div>
                               {else}
                                   <div>
                                       {'mPDF v5 is installed!'|gettext}
                                   </div>
                               {/if}
                               <blockquote>
                                   {'MPDF v5 is an optional package, but a preferred generator. To obtain it, you must first download, then install it using one of the methods below.'|gettext}
                                   <ol>
                                       <li>{'Download the basic library'|gettext} <a href="https://github.com/mpdf/mpdf/archive/v5.7.4a.zip" target="_blank">v5.7.4a.zip</a>
                                           {'and then extract it on your server into the \'external\' folder.'|gettext}</li>
                                       <li>{'(or) Download the Exponent Extension package'|gettext} <a href="http://sourceforge.net/projects/exponentcms/files/Add-ons/mpdf57a.zip/download" target="_blank">mpdf57a.zip</a>.
                                           {'and then'|gettext} <a href="install_extension">{'Install New Extension'|gettext}</a> {'on your server with \'Patch Exponent CMS\' checked.'|gettext}</li>
                                   </ol>
                               </blockquote>
                           </div>
                           <div id="expMPDF6-div" class="alt-item" style="display:none;">
                               {if !file_exists("`$smarty.const.BASE`external/mpdf60/mpdf.php")}
                                   <div style="color:#ff0000;font-weight:bold;">
                                       {'mPDF v6 is NOT installed!'|gettext}
                                   </div>
                               {else}
                                   <div>
                                       {'mPDF v6 is installed!'|gettext}
                                   </div>
                               {/if}
                               <blockquote>
                                   {'MPDF v6 is an optional package, but the preferred generator.  To obtain it, you must first download, then install it using the method below.'|gettext}
                                   <ol>
                                       <li>{'Download the basic library'|gettext} <a href="https://github.com/mpdf/mpdf/archive/v6.0.0.zip" target="_blank">MPDF60.zip</a>
                                           {'and then extract it on your server into the \'external\' folder.'|gettext}</li>
                                       <li>{'(or) Download the Exponent Extension package'|gettext} <a href="http://sourceforge.net/projects/exponentcms/files/Add-ons/mpdf60a.zip/download" target="_blank">mpdf60a.zip</a>.
                                           {'and then'|gettext} <a href="install_extension">{'Install New Extension'|gettext}</a> {'on your server with \'Patch Exponent CMS\' checked.'|gettext}</li>
                                   </ol>
                               </blockquote>
                           </div>
                           <div id="expMPDF61-div" class="alt-item" style="display:none;">
                               {if !file_exists("`$smarty.const.BASE`external/mpdf-6.1.1/mpdf.php")}
                                   <div style="color:#ff0000;font-weight:bold;">
                                       {'mPDF v6.1 is NOT installed!'|gettext}
                                   </div>
                               {else}
                                   <div>
                                       {'mPDF v6.1 is installed!'|gettext}
                                   </div>
                               {/if}
                               <blockquote>
                                   {'MPDF v6.1 is an optional package, but the preferred generator.  To obtain it, you must first download, then install it using the method below.'|gettext}
                                   <ol>
                                       <li>{'Download the basic library'|gettext} <a href="https://github.com/mpdf/mpdf/archive/v6.1.1.zip" target="_blank">MPDF6.1.1.zip</a>
                                           {'and then extract it on your server into the \'external\' folder.'|gettext}</li>
                                   </ol>
                               </blockquote>
                           </div>
                           <div id="expDOMPDF-div" class="alt-item" style="display:none;">
                               {if !file_exists("`$smarty.const.BASE`external/dompdf/dompdf.php")}
                                   <div style="color:#ff0000;font-weight:bold;">
                                       {'dompdf v0.6 is NOT installed!'|gettext}
                                   </div>
                               {else}
                                   <div>
                                       {'dompdf v0.6 is installed!'|gettext}
                                   </div>
                               {/if}
                               <blockquote>
                                   {'DOMPDF v0.6 is an optional package.  To obtain it, you must first download our customized version of the library'|gettext} <a href="https://sourceforge.net/projects/exponentcms/files/Add-ons/dompdf062a.zip/download" target="_blank">dompdf062a.zip</a>.
                                   {'and then'|gettext} <a href="install_extension">{'Install New Extension'|gettext}</a> {'on your server with \'Patch Exponent CMS\' checked.'|gettext}
                               </blockquote>
                           </div>
                           <div id="expDOMPDF070-div" class="alt-item" style="display:none;">
                               {if !file_exists("`$smarty.const.BASE`external/dompdf/dompdf.php")}
                                   <div style="color:#ff0000;font-weight:bold;">
                                       {'dompdf v0.7 is NOT installed!'|gettext}
                                   </div>
                               {else}
                                   <div>
                                       {'dompdf v0.7 is installed!'|gettext}
                                   </div>
                               {/if}
                               <blockquote>
                                   {'DOMPDF v0.7 is an optional package.  To obtain it, you must first download our customized version of the library'|gettext} <a href="https://sourceforge.net/projects/exponentcms/files/Add-ons/dompdf070.zip/download" target="_blank">dompdf070.zip</a>.
                                   {'and then'|gettext} <a href="install_extension">{'Install New Extension'|gettext}</a> {'on your server with \'Patch Exponent CMS\' checked.'|gettext}
                               </blockquote>
                           </div>
                           <div id="expHTML2PDF-div" class="alt-item" style="display:none;">
                               {if !file_exists("`$smarty.const.BASE`external/html2pdf/html2pdf.class.php") || !file_exists("`$smarty.const.BASE`external/TCPDF/tcpdf.php")}
                                   <div style="color:#ff0000;font-weight:bold;">
                                       {'HTML2PDF/TCPDF is NOT installed!'|gettext}
                                   </div>
                               {else}
                                   <div>
                                       {'HTML2PDF/TCPDF is installed!'|gettext}
                                   </div>
                               {/if}
                               <blockquote>
                                   {'HTML2PDF is an optional package.  To obtain it, you must first download our customized version of the library'|gettext} <a href="http://sourceforge.net/projects/exponentcms/files/Add-ons/html2pdf.zip/download" target="_blank">html2pdf.zip</a>.
                                   {'and then'|gettext} <a href="install_extension">{'Install New Extension'|gettext}</a> {'on your server with \'Patch Exponent CMS\' checked.'|gettext}
                               </blockquote>
                           </div>
                           <div id="expWKPDF-div" class="alt-item" style="display:none;">
                               {if !file_exists("$smarty.const.HTMLTOPDF_PATH")}
                                   <div style="color:#ff0000;font-weight:bold;">
                                       {'WKHTMLtoPDF is NOT installed/configured!'|gettext}
                                   </div>
                               {else}
                                   <div>
                                       {'WKHTMLtoPDF is installed!'|gettext}
                                   </div>
                               {/if}
                               {control type="text" name="sc[HTMLTOPDF_PATH]" label="Full Path to the WKHTMLtoPDF Binary Utility"|gettext value=$smarty.const.HTMLTOPDF_PATH}
                               {control type="text" name="sc[HTMLTOPDF_PATH_TMP]" label="Full Path to the WKHTMLtoPDF Temp Directory"|gettext value=$smarty.const.HTMLTOPDF_PATH_TMP}
                               <blockquote>
                                   {'To obtain the WKHTMLtoPDF, you\'ll need to first download the appropriate binary application from'|gettext} <a href="http://wkhtmltopdf.org/downloads.html" target="_blank">{"wkhtmltopdf site"|gettext}</a>.
                                   {"and then install it on your server."|gettext}
                               </blockquote>
                           </div>
                           {control type="checkbox" postfalse=1 name="sc[HTMLTOPDF_OUTPUT]" label="Force PDF File Download?"|gettext checked=$smarty.const.HTMLTOPDF_OUTPUT value=1 description='Force a file download instead of display in window'|gettext}
                       </div>
                   </div>
                </div>
				<div id="tab14" role="tabpanel" class="tab-pane fade">
					<div class="info-header">
			            <div class="related-actions">
				            {help text="Get Help with"|gettext|cat:" "|cat:("minification settings"|gettext) module="minify-configuration"}
			            </div>
			            <h2>{"Minify Configuration"|gettext}</h2>
			        </div>
                    {control type="text" name="sc[MINIFY_MAXAGE]" label="Maximum age of browser cache in seconds"|gettext value=$smarty.const.MINIFY_MAXAGE}
					{control type="text" name="sc[MINIFY_MAX_FILES]" label='Maximum # of files that can be specified in the \'f\' GET parameter'|gettext value=$smarty.const.MINIFY_MAX_FILES}
					{control type="text" name="sc[MINIFY_URL_LENGTH]" label="The length of minification url"|gettext value=$smarty.const.MINIFY_URL_LENGTH}
                    {group label="Minify Debugging Settings"|gettext}
                        {control type="checkbox" postfalse=1 name="sc[MINIFY_ERROR_LOGGER]" label="Enable logging of minify error messages to FirePHP?"|gettext checked=$smarty.const.MINIFY_ERROR_LOGGER value=1}
                        {control type="checkbox" postfalse=1 name="sc[MINIFY_INLINE_CSS]" label="Minify inline css styles?"|gettext checked=$smarty.const.MINIFY_INLINE_CSS value=1}
                        {control type="checkbox" postfalse=1 name="sc[MINIFY_LESS]" label="Minify .less compiled style-sheets?"|gettext checked=$smarty.const.MINIFY_LESS value=1}
                        {control type="checkbox" postfalse=1 name="sc[MINIFY_LINKED_CSS]" label="Minify and Combine linked css style-sheets?"|gettext checked=$smarty.const.MINIFY_LINKED_CSS value=1}
                        {control type="checkbox" postfalse=1 name="sc[MINIFY_INLINE_JS]" label="Minify inline javascript?"|gettext checked=$smarty.const.MINIFY_INLINE_JS value=1}
                        {control type="checkbox" postfalse=1 name="sc[MINIFY_LINKED_JS]" label="Minify and Combine linked js scripts?"|gettext checked=$smarty.const.MINIFY_LINKED_JS value=1}
                        {control type="checkbox" postfalse=1 name="sc[MINIFY_YUI3]" label="Combine YUI3 items?"|gettext checked=$smarty.const.MINIFY_YUI3 value=1}
                        {control type="checkbox" postfalse=1 name="sc[MINIFY_YUI2]" label="Combine YUI2 items?"|gettext checked=$smarty.const.MINIFY_YUI2 value=1}
                    {/group}
                </div>
				<div id="tab15" role="tabpanel" class="tab-pane fade">
                    <div class="info-header">
                        <div class="related-actions">
                            {help text="Get Help with"|gettext|cat:" "|cat:("search report settings"|gettext) module="search-report-settings"}
                        </div>
                        <h2>{"Search Report Configuration"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[SAVE_SEARCH_QUERIES]" label="Save Search Queries?"|gettext checked=$smarty.const.SAVE_SEARCH_QUERIES value=1}
					{control type="text" name="sc[TOP_SEARCH]" label="Number of Top Search Queries to Return"|gettext value=$smarty.const.TOP_SEARCH}
					{control type="checkbox" postfalse=1 name="sc[INCLUDE_AJAX_SEARCH]" label="Include ajax search in reports?"|gettext checked=$smarty.const.INCLUDE_AJAX_SEARCH value=1}
					{control type="checkbox" postfalse=1 name="sc[INCLUDE_ANONYMOUS_SEARCH]" label="Include unregistered users search?"|gettext checked=$smarty.const.INCLUDE_ANONYMOUS_SEARCH value=1}
				</div>
                <div id="tab16" role="tabpanel" class="tab-pane fade">
                    <div class="info-header">
                        <div class="related-actions">
                            {help text="Get Help with"|gettext|cat:" "|cat:("e-Commerce settings"|gettext) module="ecommerce-configuration"}
                        </div>
                        <h2>{"e-Commerce Configuration"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[FORCE_ECOM]" label="Activate e-Commerce?"|gettext checked=$smarty.const.FORCE_ECOM value=1}
                    {control type="checkbox" postfalse=1 name="sc[ECOM_LARGE_DB]" label="Allow Large e-Commerce Tables?"|gettext checked=$smarty.const.ECOM_LARGE_DB value=1 description='This will prevent manage product/order problems, but disable the filter/search features'|gettext}
                    {control type="checkbox" postfalse=1 name="sc[DISABLE_SSL_WARNING]" label="Disable Unsecure Checkout Warning?"|gettext checked=$smarty.const.DISABLE_SSL_WARNING value=1 description='Normally a warning is displayed when attempting to checkout on an unsecured site.'|gettext}
                    {control type="dropdown" name="sc[ECOM_CURRENCY]" label="Default Currency"|gettext items=$currency default=$smarty.const.ECOM_CURRENCY}
                    {group label="Getting e-Commerce up and running"|gettext}
                        <ol>
                            <li><strong>{'Set up your site on a secure (SSL) server!'|gettext}</strong></li>
                            <ul>
                                <li>{'Enter appropriate settings under the Security tab above.'|gettext}</li>
                            </ul>
                            <li>{'Import default ecommerce information into the database'|gettext} <a href="{link action=install_ecommerce_tables}" title={'Install Default e-Commerce data'|gettext} onclick="return confirm('{'Are you sure you want to re-initialize e-Commerce data to default values?'|gettext}');">{'here'|gettext}</a></li>
                            <ul>
                                <li>geo_regions</li>
                                <li>geo_countries</li>
                                <li>order_status</li>
                                <li>order_type</li>
                                <li>product_status</li>
                                <li>bing_product_types</li>
                                <li>google_product_types</li>
                                <li>nextag_product_types</li>
                                <li>pricegrabber_product_types</li>
                                <li>shopping_product_types</li>
                                <li>shopzilla_product_types</li>
                            </ul>
                            <li>{'Activate e-Commerce using the above setting, or activate an e-Commerce module'|gettext}:</li>
                            <ul>
                                <li>{'e-Commerce Store Front'|gettext}</li>
                                <li>{'Online Donations'|gettext}</li>
                                <li>{'Online Event Registration'|gettext}</li>
                            </ul>
                            <li>{'Activate a Payment Option'|gettext} <a href="{link controller=billing action=manage}" title={'Configure Billing Settings'|gettext}>{'here'|gettext}</a></li>
                            <ul>
                                <li>{'Most Payment Options need configuration which requires establishing an account with a payment service'|gettext}</li>
                                <li>{'\'Bill Me\' is the easiest to set up'|gettext}</li>
                            </ul>
                            <li>{'Activate a Shipping Option'|gettext} <a href="{link controller=shipping action=manage}" title={'Configure Shipping Information'|gettext}>{'here'|gettext}</a></li>
                            <ul>
                                <li>{'Most Shipping Options need configuration which requires establishing an account with a shipping service'|gettext}</li>
                                <li>{'\'In Store Pickup\' is the easiest to set up'|gettext}</li>
                            </ul>
                            <li>{'Optionally (to get better results)'|gettext}:</li>
                            <ul>
                                <li>{'Enter some \'General Store Settings\''|gettext} <a href="{link controller=ecomconfig action=configure}" title={'Configure Store Settings'|gettext}>{'here'|gettext}</a></li>
                                <ul>
                                    <li>{'You should at least enter a Store Name and Starting Invoice Number'|gettext}</li>
                                </ul>
                                <li>{'Create a Product (with optional sub-steps)'|gettext}</li>
                                <ul>
                                    <li>{'Create a Store Category'|gettext} <a href="{link controller=storeCategory action=manage}" title={'Manage Store Categories'|gettext}>{'here'|gettext}</a></li>
                                    <li>{'Create a Manufacturer'|gettext} <a href="{link controller=company action=showall}" title={'Manage Manufacturers'|gettext}>{'here'|gettext}</a></li>
                                    <li>{'Create a Tax Class/Zone/Rate for applicable sales tax(es)'|gettext} <a href="{link controller=tax action=manage}" title={'Manage Taxes'|gettext}>{'here'|gettext}</a></li>
                                    <li>{'Create the Product (product, donation, event, or gift card) and assign a category'|gettext} <a href="{link controller=store action=edit}" title={'Add a Product'|gettext}>{'here'|gettext}</a></li>
                                </ul>
                            </ul>
                            <li>{'Add an e-Commerce module to a page to allow user access to the \'store\'.'|gettext}</li>
                            <ul>
                                <li>{'Also add an \'e-Commerce Store Front\' module with the \'Links - Users Links\' action for easier user access to their store account and shopping cart'|gettext}</li>
                            </ul>
                            <li>{'e-Commerce Store Management is best handled through the e-Commerce menu or Dashboard'|gettext}</li>
                        </ol>
                    {/group}
                </div>
                <div id="tab17" role="tabpanel" class="tab-pane fade">
                    <div class="info-header">
                        <div class="related-actions">
                            {help text="Get Help with"|gettext|cat:" "|cat:("configuration profiles"|gettext) module="configuration-profiles"}
                        </div>
                        <h2>{"Configuration Profiles"|gettext}</h2>
                    </div>
                    {control type="dropdown" name="profiles" label="Load configuration profile"|gettext items=$profiles default=$smarty.const.CURRENTCONFIGNAME onchange="changeProfile(this.value)"}
                    {control type="text" name="profile_name" label="New Profile Name"|gettext value=$smarty.const.CURRENTCONFIGNAME}
                    {*<a class="{button_style}" href="#" onclick="saveProfile()"><strong>{'Save New Profile'|gettext}</strong></a>*}
                    {icon button=true class=save action=scriptaction onclick="saveProfile()" text='Save New Profile'|gettext}
                    {br}{br}
                </div>
                {/if}
            </div>
        </div>
	    {*<div class="loadingdiv">{"Loading Site Configuration"|gettext}</div>*}
        {loading title="Loading Site Configuration"|gettext}
        {control type="buttongroup" submit="Save Website Configuration"|gettext cancel="Cancel"|gettext returntype="viewable"}
    {/form}
</div>

{script unique="`$config`"}
{literal}
    function changeProfile(val) {
        var configname = document.getElementById("profiles").value;
        if (confirm('{/literal}{'Are you sure you want to load a new profile?'|gettext}{literal}'+' ('+configname+')')) {
            window.location = EXPONENT.PATH_RELATIVE+"administration/change_profile/profile/" + val;
        } else {
            document.getElementById("profiles").value = '';
        }
    }

    function saveProfile() {
        if (document.getElementById("profile_name").value != '') {
            if (confirm('{/literal}{'Are you sure you want to save this configuration profile?'|gettext}{literal}')) {
                window.location = EXPONENT.PATH_RELATIVE+"administration/save_profile/profile/" + document.getElementById("profile_name").value;
            }
        }
    }
{/literal}
{/script}

{script unique="editchecks" jquery=1}
{literal}
$('#use_antispam').change(function() {
    if ($('#use_antispam').is(':checked') == false)
        $("#antispam").hide("slow");
    else {
        $("#antispam").show("slow");
    }
});
if ($('#use_antispam').is(':checked') == false)
    $("#antispam").hide("slow");

$('#use_ldap').change(function() {
    if ($('#use_ldap').is(':checked') == false)
        $("#ldap").hide("slow");
    else {
        $("#ldap").show("slow");
    }
});
if ($('#use_ldap').is(':checked') == false)
    $("#ldap").hide("slow");

$('#no_smtp').change(function() {
    if ($('#no_smtp').is(':checked') == true)
        $("#smtp").hide("slow");
    else {
        $("#smtp").show("slow");
    }
});
if ($('#no_smtp').is(':checked') == true)
    $("#smtp").hide("slow");
{/literal}
{/script}

{script unique="wysiwyg-type" jquery=1}
{literal}
$(document).ready(function(){
    var radioSwitcher_wysiwyg = $('#alt-control-wysiwyg input[type="radio"]');
    radioSwitcher_wysiwyg.on('click', function(e){
        $("#alt-control-wysiwyg .alt-item").css('display', 'none');
        var curdiv = $("#" + e.target.value + "-div");
        curdiv.css('display', 'block');
    });

    radioSwitcher_wysiwyg.each(function(k, node){
        if(node.checked == true){
            $(node).trigger('click');
        }
    });
});
{/literal}
{/script}

{script unique="pdf-type" jquery=1}
{literal}
$(document).ready(function(){
    var radioSwitchers_pdf = $('#alt-control-pdf input[type="radio"]');
    radioSwitchers_pdf.on('click', function(e){
        $("#alt-control-pdf .alt-item").css('display', 'none');
        var curdiv = $("#" + e.target.value + "-div");
        curdiv.css('display', 'block');
    });

    radioSwitchers_pdf.each(function(k, node){
        if(node.checked == true){
            $(node).trigger('click');
        }
    });
});
{/literal}
{/script}

{script unique="tabload" jquery=1 bootstrap="tab,transition"}
{literal}
    $('.loadingdiv').remove();
{/literal}
{/script}