{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

<div id="siteconfig" class="module administration configure-site">
	<div class="form_header">
		<div class="info-header">
			<div class="related-actions">
			    {help text="Get Help with"|gettext|cat:" "|cat:("configuring your website"|gettext) page="site-configuration"}
			</div>
			<h1>{'Configure Website'|gettext}</h1>
		</div>
	</div>
    {form controller="administration" action=update_siteconfig}
        <div id="{$config}" class="yui-navset exp-skin-tabview hide">
            <ul class="yui-nav">
	            <li class="selected"><a href="#tab1"><em>{"General"|gettext}</em></a></li>
	            <li><a href="#tab2"><em>{"Anti-Spam"|gettext}</em></a></li>
	            <li><a href="#tab3"><em>{"User Registration"|gettext}</em></a></li>
	            <li><a href="#tab4"><em>{"Comment Policies"|gettext}</em></a></li>
	            <li><a href="#tab5"><em>{"Display"|gettext}</em></a></li>
                <li><a href="#tab6"><em>{"File Manager"|gettext}</em></a></li>
	            {if $user->is_admin==1}
					<li><a href="#tab7"><em>{"Mail Server"|gettext}</em></a></li>
		            <li><a href="#tab8"><em>{"Maintenance"|gettext}</em></a></li>
		            <li><a href="#tab9"><em>{"Security"|gettext}</em></a></li>
					<li><a href="#tab10"><em>{"Help Links"|gettext}</em></a></li>
					<li><a href="#tab11"><em>{"WYSIWYG Editor"|gettext}</em></a></li>
		            <li><a href="#tab12"><em>{"Error Messages"|gettext}</em></a></li>
		            <li><a href="#tab13"><em>{"PDF Generation"|gettext}</em></a></li>
					<li><a href="#tab14"><em>{"Minify"|gettext}</em></a></li>
					<li><a href="#tab15"><em>{"Search Report"|gettext}</em></a></li>
                    <li><a href="#tab16"><em>{"e-Commerce"|gettext}</em></a></li>
	            {/if}
            </ul>            
            <div class="yui-content">
                <div id="tab1">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("general site configuration settings"|gettext) module="general-configuration"}
                        </div>
		                <h2>{"General Site Configuration"|gettext}</h2>
                    </div>
                    {control type="text" name="sc[ORGANIZATION_NAME]" label="Site/Organization Name"|gettext value=$smarty.const.ORGANIZATION_NAME}
                    {control type="text" name="sc[SITE_TITLE]" label="Site Title"|gettext value=$smarty.const.SITE_TITLE}
					{control type="text" name="sc[SITE_HEADER]" label="Site Header"|gettext value=$smarty.const.SITE_HEADER}
                    {control type="checkbox" postfalse=1 name="sc[SEF_URLS]" label="Search Engine Friendly URLs?"|gettext checked=$smarty.const.SEF_URLS value=1}
					{control type="checkbox" postfalse=1 name="sc[ADVERTISE_RSS]" label="Advertise RSS Feeds to Web Browsers?"|gettext checked=$smarty.const.ADVERTISE_RSS value=1}
                    {control type="checkbox" postfalse=1 name="sc[SKIP_VERSION_CHECK]" label="Skip Automatic Online Version Update Check?"|gettext checked=$smarty.const.SKIP_VERSION_CHECK value=1 description='You can still check for an updated version using the Exponent, Super-Admin Tools menu'|gettext}
                    {control type="dropdown" name="sc[SITE_DEFAULT_SECTION]" label="Default Section (Home Page)"|gettext items=$section_dropdown default=$smarty.const.SITE_DEFAULT_SECTION}
                    {control type="textarea" name="sc[SITE_KEYWORDS]" label='('|cat:('Meta'|gettext)|cat:') '|cat:('Keywords'|gettext) value=$smarty.const.SITE_KEYWORDS}
	                {control type="textarea" name="sc[SITE_DESCRIPTION]" label='('|cat:('Meta'|gettext)|cat:') '|cat:('Description'|gettext) value=$smarty.const.SITE_DESCRIPTION}
                </div>
                <div id="tab2">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("anti-spam measure settings"|gettext) module="anti-spam-measures"}
                        </div>
		                <h2>{"Anti-Spam Measures"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[SITE_USE_ANTI_SPAM]" label="Use Anti-Spam measures?"|gettext checked=$smarty.const.SITE_USE_ANTI_SPAM value=1}
                    {control type="checkbox" postfalse=1 name="sc[ANTI_SPAM_USERS_SKIP]" label="Skip using Anti-Spam measures for Logged-In Users?"|gettext checked=$smarty.const.ANTI_SPAM_USERS_SKIP value=1}
                    {control type="dropdown" name="sc[ANTI_SPAM_CONTROL]" label="Anti-Spam Method"|gettext items=$as_types default=$smarty.const.ANTI_SPAM_CONTROL}
                    <blockquote>
	                {'To obtain the reCAPTCHA \'keys\', you\'ll need to first have a'|gettext} <a href="http://www.google.com/" target="_blank">{"Google account"|gettext}</a> {"to log in, then setup up a reCAPTCHA account for your domain(s)"|gettext} <a href="http://www.google.com/recaptcha/whyrecaptcha" target="_blank">{"here"|gettext}</a>
                    </blockquote>
                    {control type="dropdown" name="sc[RECAPTCHA_THEME]" label="re-Captcha Theme"|gettext items=$as_themes default=$smarty.const.RECAPTCHA_THEME}
                    {control type="text" name="sc[RECAPTCHA_PUB_KEY]" label="reCAPTCHA Public Key"|gettext value=$smarty.const.RECAPTCHA_PUB_KEY}
                    {control type="text" name="sc[RECAPTCHA_PRIVATE_KEY]" label="reCAPTCHA Private Key"|gettext value=$smarty.const.RECAPTCHA_PRIVATE_KEY}
                </div>
                <div id="tab3">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("user registration settings"|gettext) module="user-registration"}
                        </div>
		                <h2>{"User Registration"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[SITE_ALLOW_REGISTRATION]" label="Should users be allowed to create accounts for themselves?"|gettext checked=$smarty.const.SITE_ALLOW_REGISTRATION value=1}
                    {control type="checkbox" postfalse=1 name="sc[USER_REGISTRATION_USE_EMAIL]" label="Use an email address instead of a username?"|gettext checked=$smarty.const.USER_REGISTRATION_USE_EMAIL value=1}
                    {control type="checkbox" postfalse=1 name="sc[USER_NO_PASSWORD_CHANGE]" label="Disable User Request Password Change Feature?"|gettext checked=$smarty.const.USER_NO_PASSWORD_CHANGE value=1}
                    {group label="New User Notification Email"|gettext}
                    {control type="checkbox" postfalse=1 name="sc[USER_REGISTRATION_SEND_NOTIF]" label="Notify a site administrator when a new user registers on your website?"|gettext checked=$smarty.const.USER_REGISTRATION_SEND_NOTIF value=1}
                    {control type="text" name="sc[USER_REGISTRATION_NOTIF_SUBJECT]" label='Subject of the administrator\'s new user notification'|gettext value=$smarty.const.USER_REGISTRATION_NOTIF_SUBJECT}
                    {*{control type="text" name="sc[USER_REGISTRATION_ADMIN_EMAIL]" label="Email address of administrator that should be notified when a user signs up"|gettext value=$smarty.const.USER_REGISTRATION_ADMIN_EMAIL}*}
                    {control type=email name="sc[USER_REGISTRATION_ADMIN_EMAIL]" label="Email address of administrator that should be notified when a user signs up"|gettext value=$smarty.const.USER_REGISTRATION_ADMIN_EMAIL}
                    {/group}
                    {group label="New User Welcome Message"|gettext}
                    {control type="checkbox" postfalse=1 name="sc[USER_REGISTRATION_SEND_WELCOME]" label="Send an email to the user after registering?"|gettext checked=$smarty.const.USER_REGISTRATION_SEND_WELCOME value=1}
                    {control type="text" name="sc[USER_REGISTRATION_WELCOME_SUBJECT]" label="Welcome Email Subject"|gettext value=$smarty.const.USER_REGISTRATION_WELCOME_SUBJECT}
                    {control type="textarea" name="sc[USER_REGISTRATION_WELCOME_MSG]" label="Welcome Email Content"|gettext value=$smarty.const.USER_REGISTRATION_WELCOME_MSG}
                    {/group}
                    {if function_exists('ldap_connect')}
                    {group label="LDAP Authentication"|gettext}
                    {control type="checkbox" postfalse=1 name="sc[USE_LDAP]" label="Turn on LDAP Authentication?"|gettext checked=$smarty.const.USE_LDAP value=1 description=gt('Checking this option will cause Exponent to try to authenticate to the ldap server listed below.')}
                    {control type="text" name="sc[LDAP_SERVER]" label="LDAP Server"|gettext value=$smarty.const.LDAP_SERVER description=gt('Enter the hostname or IP of the LDAP server.')}
                    {control type="text" name="sc[LDAP_BASE_CONTEXT]" label="Base Context"|gettext value=$smarty.const.LDAP_BASE_CONTEXT description=gt('Enter the Base Context for this LDAP connection. (e.g., ou=users, dc=mycompanysite, dc=local)')}
                    {control type="text" name="sc[LDAP_BASE_DN]" label="Base Domain"|gettext value=$smarty.const.LDAP_BASE_DN description=gt('Enter the Base Domain for this LDAP connection. (e.g., mycompanysite.local)')}
                    {*{control type="text" name="sc[LDAP_BIND_USER]" label="LDAP Bind User"|gettext value=$smarty.const.LDAP_BIND_USER description=gt('The username or context for the binding to the LDAP Server to perform administration tasks (This currently doesn\'t do anything).')}*}
                    {*{control type="password" name="sc[LDAP_BIND_PASS]" label="LDAP Bind Password"|gettext value=$smarty.const.LDAP_BIND_PASS description=gt('Enter the password for the username/context listed above.')}*}
                    {/group}
                    {/if}
                </div>
                <div id="tab4">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("user comment policy settings"|gettext) module="user-comment-policies"}
                        </div>
		                <h2>{"User Comment Policies"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[COMMENTS_REQUIRE_LOGIN]" label="Require User Login to Post Comments?"|gettext checked=$smarty.const.COMMENTS_REQUIRE_LOGIN value=1}
                    {control type="checkbox" postfalse=1 name="sc[COMMENTS_REQUIRE_APPROVAL]" label="All Comments Must be Approved?"|gettext checked=$smarty.const.COMMENTS_REQUIRE_APPROVAL value=1}
                    {control type="checkbox" postfalse=1 name="sc[COMMENTS_REQUIRE_NOTIFICATION]" label="Notify a site administrator of New Comments?"|gettext checked=$smarty.const.COMMENTS_REQUIRE_NOTIFICATION value=1}
                    {*{control type="text" name="sc[COMMENTS_NOTIFICATION_EMAIL]" label="Email address(es) that should be notified of New Comments"|gettext description="Enter multiple addresses by using a comma to separate them"|gettext value=$smarty.const.COMMENTS_NOTIFICATION_EMAIL}*}
                    {control type=email multiple="1" name="sc[COMMENTS_NOTIFICATION_EMAIL]" label="Email address(es) that should be notified of New Comments"|gettext description="Enter multiple addresses by using a comma to separate them"|gettext value=$smarty.const.COMMENTS_NOTIFICATION_EMAIL}
                </div>
                <div id="tab5">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("display settings"|gettext) module="display-settings"}
                        </div>
		                <h2>{"Display Settings"|gettext}</h2>
                    </div>
                    {control type="dropdown" name="sc[LANGUAGE]" label="Display Language"|gettext items=$langs default=$smarty.const.LANGUAGE}
                    {*{control type="dropdown" name="sc[DISPLAY_THEME_REAL]" label="Theme <a href=\"manage_themes\">(More Theme Options)</a>"|gettext items=$themes default=$smarty.const.DISPLAY_THEME_REAL}*}
	                <h3><a href="manage_themes">{'Display Theme Options'|gettext}</a></h3>
	                {control type="checkbox" postfalse=1 name="sc[INVERT_HIDE_TITLE]" label="Reverse the Logic of Hide Module Title setting?"|gettext checked=$smarty.const.INVERT_HIDE_TITLE value=1 description='Changes default of always show title to always hide title, unless module setting is checked.'|gettext}
                    {control type="checkbox" postfalse=1 name="sc[FORCE_MOBILE]" label="Force Display of the Mobile Theme Variation (if available)?"|gettext checked=$smarty.const.FORCE_MOBILE value=1}
                    {group label="Display Formats"|gettext}
                    {control type="dropdown" name="sc[DISPLAY_ATTRIBUTION]" label="Attribution Display"|gettext items=$attribution default=$smarty.const.DISPLAY_ATTRIBUTION}
	                {control type="dropdown" name="sc[DISPLAY_DATETIME_FORMAT]" label="Date/Time Format"|gettext items=$datetime_format default=$smarty.const.DISPLAY_DATETIME_FORMAT}
                    {control type="dropdown" name="sc[DISPLAY_DATE_FORMAT]" label="Date Format"|gettext items=$date_format default=$smarty.const.DISPLAY_DATE_FORMAT}
                    {control type="dropdown" name="sc[DISPLAY_TIME_FORMAT]" label="Time Format"|gettext items=$time_format default=$smarty.const.DISPLAY_TIME_FORMAT}
                    {control type="dropdown" name="sc[DISPLAY_START_OF_WEEK]" label="Start of Week"|gettext items=$start_of_week default=$smarty.const.DISPLAY_START_OF_WEEK}
                    {/group}
	                {control type="dropdown" name="sc[DISPLAY_DEFAULT_TIMEZONE]" label="Default timezone for this site"|gettext|cat:(' <br />'|cat:("CAUTION: Changes may affect calendars and other features using date functions."|gettext)) items=$timezones default=$smarty.const.DISPLAY_DEFAULT_TIMEZONE}
                    {control type="radiogroup" name="sc[SLINGBAR_TOP]" label="Default Admin Slingbar Position" items="Top of Viewport,Bottom of Viewport"|gettxtlist values="1,0" default=$smarty.const.SLINGBAR_TOP}
					{control type="text" name="sc[THUMB_QUALITY]" label="Thumbnail JPEG Quality"|gettext|cat:" (0 - 95)" value=$smarty.const.THUMB_QUALITY|default:75 size="2"}
                </div>
                <div id="tab6">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("file manager settings"|gettext) module="filemanager-settings"}
                        </div>
		                <h2>{"File Manager/Uploader Settings"|gettext}</h2>
                    </div>
                    {control type="text" name="sc[FM_WIDTH]" label="Popup Window Width"|gettext value=$smarty.const.FM_WIDTH|default:1024 size="4"}
                    {control type="text" name="sc[FM_HEIGHT]" label="Popup Window Height" value=$smarty.const.FM_HEIGHT|default:600 size="4"}
                    {control type="text" name="sc[FM_LIMIT]" label="Number of Files per Page" value=$smarty.const.FM_LIMIT|default:25 size="4"}
                    {control type="text" name="sc[FM_SIMLIMIT]" label="Number of Simultaneous Uploads" value=$smarty.const.FM_SIMLIMIT|default:3 size="2"}
                    {control type="checkbox" postfalse=1 name="sc[FM_THUMBNAILS]" label="Show Image Thumbnails?"|gettext checked=$smarty.const.FM_THUMBNAILS value=1}
                    {control type="text" name="sc[FM_THUMB_SIZE]" label="Thumbnail Size"|gettext value=$smarty.const.FM_THUMB_SIZE|default:48 size="4"}
                    {control type="text" name="sc[UPLOAD_WIDTH]" label="Uploader Default Max Width/Height to Downsize Graphics"|gettext value=$smarty.const.UPLOAD_WIDTH|default:400 size="4"}
                </div>
                {if $user->is_admin==1}
                <div id="tab7">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("mail server settings"|gettext) module="mail-server-settings"}
                        </div>
		                <h2>{"Mail Server Settings"|gettext}</h2>
                    </div>
	                {*{control type="text" name="sc[SMTP_FROMADDRESS]" label="From Address"|gettext value=$smarty.const.SMTP_FROMADDRESS description='This MUST be in a valid email address format or sending mail may fail!'|gettext}*}
                    {control type=email name="sc[SMTP_FROMADDRESS]" label="From Address"|gettext value=$smarty.const.SMTP_FROMADDRESS description='This MUST be in a valid email address format or sending mail may fail!'|gettext}
                    {control type="checkbox" postfalse=1 name="sc[SMTP_USE_PHP_MAIL]" label='Use simplified php mail() function instead of SMTP?'|gettext checked=$smarty.const.SMTP_USE_PHP_MAIL value=1}
	                ({"or"|gettext})
                    {group label="SMTP Server Settings"|gettext}
                    {control type="text" name="sc[SMTP_SERVER]" label="SMTP Server"|gettext value=$smarty.const.SMTP_SERVER}
                    {control type="text" name="sc[SMTP_PORT]" label="SMTP Port"|gettext value=$smarty.const.SMTP_PORT}
                    {control type="dropdown" name="sc[SMTP_PROTOCOL]" label="Type of Encrypted Connection"|gettext items=$protocol default=$smarty.const.SMTP_PROTOCOL includeblank="None"}
                    {control type="text" name="sc[SMTP_USERNAME]" label="SMTP Username"|gettext value=$smarty.const.SMTP_USERNAME}
                    {control type="text" name="sc[SMTP_PASSWORD]" label="SMTP Password"|gettext value=$smarty.const.SMTP_PASSWORD}
	                {control type="checkbox" postfalse=1 name="sc[SMTP_DEBUGGING]" label="Turn On SMTP Debugging?"|gettext checked=$smarty.const.SMTP_DEBUGGING value=1}
                    {/group}
                </div>
                <div id="tab8">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("site maintenance mode settings"|gettext) module="site-maintenance-mode-settings"}
                        </div>
		                <h2>{"Site Maintenance Mode Settings"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[MAINTENANCE_MODE]" label="Place Site in Maintenance Mode?"|gettext checked=$smarty.const.MAINTENANCE_MODE value=1}
                    {control type="html" name="sc[MAINTENANCE_MSG_HTML]" label="Maintenance Mode Message"|gettext value=$smarty.const.MAINTENANCE_MSG_HTML}
                </div>
                <div id="tab9">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("security settings"|gettext) module="security-settings"}
                        </div>
		                <h2>{"Security Settings"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[SESSION_TIMEOUT_ENABLE]" label="Enable Session Timeout?"|gettext checked=$smarty.const.SESSION_TIMEOUT_ENABLE value=1}
                    {control type="text" name="sc[SESSION_TIMEOUT]" label="Session Timeout in seconds"|gettext value=$smarty.const.SESSION_TIMEOUT}
                    {control type="dropdown" name="sc[FILE_DEFAULT_MODE_STR]" label="Default File Permissions"|gettext items=$file_permisions default=$smarty.const.FILE_DEFAULT_MODE_STR}
                    {control type="dropdown" name="sc[DIR_DEFAULT_MODE_STR]" label="Default Directory Permissions"|gettext items=$dir_permissions default=$smarty.const.DIR_DEFAULT_MODE_STR}
                    {control type="checkbox" postfalse=1 name="sc[ENABLE_SSL]" label="Enable SSL (https://) Support?"|gettext checked=$smarty.const.ENABLE_SSL value=1}
                    {*{control type="text" name="sc[NONSSL_URL]" label="Non-SSL URL Base"|gettext value=$smarty.const.NONSSL_URL}*}
                    {*{control type="text" name="sc[SSL_URL]" label="SSL URL Base"|gettext value=$smarty.const.SSL_URL}*}
                </div>
                <div id="tab10">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("help link settings"|gettext) module="help-link-settings"}
                        </div>
		                <h2>{"Help Link Settings"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[HELP_ACTIVE]" label="Enable Help links to online documentation?"|gettext checked=$smarty.const.HELP_ACTIVE value=1}
                    {*{control type="text" name="sc[HELP_URL]" label="URL for Help Documentation"|gettext value=$smarty.const.HELP_URL}*}
                    {control type=url name="sc[HELP_URL]" label="URL for Help Documentation"|gettext value=$smarty.const.HELP_URL}
                </div>
                <div id="tab11">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("WYSIWYG Editor Settings"|gettext) module="wysiwyg-editor-settings"}
                        </div>
		                <h2>{"WYSIWYG Editor Settings"|gettext}</h2>
                    </div>
                    {control type="dropdown" name="sc[SITE_WYSIWYG_EDITOR]" label="HTML Editor"|gettext items="CKEditor" values="ckeditor" default=$smarty.const.SITE_WYSIWYG_EDITOR}
	                {if $smarty.const.SITE_WYSIWYG_EDITOR == 'ckeditor'}
                        {group label="Editor Configuration"|gettext}
		                {chain module=expHTMLEditor view=manage}
                        {/group}
	                {/if}
                </div>
                <div id="tab12">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("error message settings"|gettext) module="error-messages"}
                        </div>
		                <h2>{"Error Messages"|gettext}</h2>
                    </div>
                    {control type="text" name="sc[SITE_404_TITLE]" label='Page Title For \'Not Found\' (404) Error'|gettext value=$smarty.const.SITE_404_TITLE}
                    {control type="html" name="sc[SITE_404_HTML]" label='\'Not Found\' (404) Error Message'|gettext value=$smarty.const.SITE_404_HTML}
                    {control type="html" name="sc[SITE_403_REAL_HTML]" label='\'Access Denied\' (403) Error Message'|gettext value=$smarty.const.SITE_403_REAL_HTML}
                    {control type="html" name="sc[SESSION_TIMEOUT_HTML]" label='\'Session Expired\' Error  Message'|gettext value=$smarty.const.SESSION_TIMEOUT_HTML}
                </div>
                <div id="tab13">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help with"|gettext|cat:" "|cat:("generating PDF settings"|gettext) module="pdf-generation"}
                        </div>
		                <h2>{"PDF Generation"|gettext}</h2>
                    </div>
                    {group label="WKHTMLtoPDF - Store Orders"|gettext}
                    {control type="text" name="sc[HTMLTOPDF_PATH]" label="Full Path to the WKHTMLtoPDF Binary Utility"|gettext value=$smarty.const.HTMLTOPDF_PATH}
                    {control type="text" name="sc[HTMLTOPDF_PATH_TMP]" label="Full Path to the WKHTMLtoPDF Temp Directory"|gettext value=$smarty.const.HTMLTOPDF_PATH_TMP}
                    <blockquote>
                    {'To obtain the WKHTMLtoPDF, you\'ll need to first download the appropriate binary application from'|gettext} <a href="http://code.google.com/p/wkhtmltopdf/downloads/list" target="_blank">{"wkhtmltopdf site"|gettext}</a>.
                        {"and then install it on your server."|gettext}
                    </blockquote>
                    {/group}
                    {group label="DOMPDF - Export as PDF"|gettext}
                    {control type="checkbox" postfalse=1 name="sc[HTML2PDF_OUTPUT]" label="Force PDF File Download?"|gettext checked=$smarty.const.HTML2PDF_OUTPUT value=1 description='Force a file download instead of display in window'|gettext}
                    <blockquote>
                    {'DOMPDF is an optional package.  To obtain it, you must first download'|gettext} <a href="https://github.com/downloads/exponentcms/exponent-cms/dompdf.zip" target="_blank">dompdf.zip</a>.
                        {'and then'|gettext} <a href="install_extension">{'Install New Extension'|gettext}</a> {'on your server with \'Patch Exponent CMS\' checked.'|gettext}
                    </blockquote>
                    {/group}
                </div>
				<div id="tab14">
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
                    {control type="checkbox" postfalse=1 name="sc[MINIFY_LINKED_CSS]" label="Minify linked css style-sheets?"|gettext checked=$smarty.const.MINIFY_LINKED_CSS value=1}
                    {control type="checkbox" postfalse=1 name="sc[MINIFY_INLINE_JS]" label="Minify inline javascript?"|gettext checked=$smarty.const.MINIFY_INLINE_JS value=1}
                    {control type="checkbox" postfalse=1 name="sc[MINIFY_LINKED_JS]" label="Minify linked js scripts?"|gettext checked=$smarty.const.MINIFY_LINKED_JS value=1}
                    {control type="checkbox" postfalse=1 name="sc[MINIFY_YUI3]" label="Minify YUI3 items?"|gettext checked=$smarty.const.MINIFY_YUI3 value=1}
                    {control type="checkbox" postfalse=1 name="sc[MINIFY_YUI2]" label="Minify YUI2 items?"|gettext checked=$smarty.const.MINIFY_YUI2 value=1}
                    {/group}
                </div>
				<div id="tab15">
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
                <div id="tab16">
                <div class="info-header">
                    <div class="related-actions">
                        {help text="Get Help with"|gettext|cat:" "|cat:("e-Commerce settings"|gettext) module="ecommerce-configuration"}
                    </div>
                    <h2>{"e-Commerce Configuration"|gettext}</h2>
                </div>
                {control type="checkbox" postfalse=1 name="sc[FORCE_ECOM]" label="Activate e-Commerce?"|gettext checked=$smarty.const.FORCE_ECOM value=1}
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
                            <li>{'\'Cash/Check\' is the easiest to set up'|gettext}</li>
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
                                <li>{'Create a Tax Class/Zone for applicable sales tax(es)'|gettext} <a href="{link controller=tax action=manage}" title={'Manage Tax Classes'|gettext}>{'here'|gettext}</a></li>
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
                {/if}
            </div>
        </div>
	    <div class="loadingdiv">{"Loading Site Configuration"|gettext}</div>
        {control type="buttongroup" submit="Save Website Configuration"|gettext cancel="Cancel"|gettext returntype="viewable"}
    {/form}
</div>

{script unique="`$config`" yui3mods=1}
{literal}
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
        Y.expTabs({srcNode: '#{/literal}{$config}{literal}'});
        Y.one('#{/literal}{$config}{literal}').removeClass('hide');
        Y.one('.loadingdiv').remove();
	});
{/literal}
{/script}
