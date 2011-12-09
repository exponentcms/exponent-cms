{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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
			    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("configuring your website"|gettext) page="site-configuration"}
			</div>
			<h1>{'Configure Website'|gettext}</h1>
		</div>
	</div>
    {form controller="administration" action=update_siteconfig}
        <div id="{$config}" class="yui-navset yui3-skin-sam hide">
            <ul class="yui-nav">
	            <li class="selected"><a href="#tab1"><em>{"General"|gettext}</em></a></li>
	            <li><a href="#tab2"><em>{"Anti-Spam"|gettext}</em></a></li>
	            <li><a href="#tab3"><em>{"User Registration"|gettext}</em></a></li>
	            <li><a href="#tab4"><em>{"Comment Policies"|gettext}</em></a></li>
	            <li><a href="#tab5"><em>{"Display"|gettext}</em></a></li>
	            {if $user->is_admin==1}
					<li><a href="#tab6"><em>{"Mail Server"|gettext}</em></a></li>
		            <li><a href="#tab7"><em>{"Maintenance"|gettext}</em></a></li>
		            <li><a href="#tab8"><em>{"Security"|gettext}</em></a></li>
					<li><a href="#tab9"><em>{"Help Links"|gettext}</em></a></li>
					<li><a href="#tab10"><em>{"WYSIWYG Editor"|gettext}</em></a></li>
		            <li><a href="#tab11"><em>{"Error Messages"|gettext}</em></a></li>
		            <li><a href="#tab12"><em>{"PDF Generation"|gettext}</em></a></li>
					<li><a href="#tab13"><em>{"Minify"|gettext}</em></a></li>
					<li><a href="#tab14"><em>{"Search Report"|gettext}</em></a></li>
	            {/if}
            </ul>            
            <div class="yui-content">
                <div id="tab1">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("general site configuration settings"|gettext) module="general-configuration"}
                        </div>
		                <h2>{"General Site Configuration"|gettext}</h2>
                    </div>
                    {control type="text" name="sc[ORGANIZATION_NAME]" label="Site/Organization Name"|gettext value=$smarty.const.ORGANIZATION_NAME}
                    {control type="text" name="sc[SITE_TITLE]" label="Site Title"|gettext value=$smarty.const.SITE_TITLE}
					{control type="text" name="sc[SITE_HEADER]" label="Site Header"|gettext value=$smarty.const.SITE_HEADER}
                    {control type="checkbox" postfalse=1 name="sc[SEF_URLS]" label="Search Engine Friendly URLs?"|gettext checked=$smarty.const.SEF_URLS value=1}
					{control type="checkbox" postfalse=1 name="sc[ADVERTISE_RSS]" label="Advertise all RSS Feeds to Web Browsers?"|gettext checked=$smarty.const.ADVERTISE_RSS value=1}
                    {control type="dropdown" name="sc[SITE_DEFAULT_SECTION]" label="Default Section (Home Page)"|gettext items=$section_dropdown default=$smarty.const.SITE_DEFAULT_SECTION}
                    {control type="textarea" name="sc[SITE_KEYWORDS]" label='('|cat:('Meta'|gettext)|cat:') '|cat:('Keywords'|gettext) value=$smarty.const.SITE_KEYWORDS}
	                {control type="textarea" name="sc[SITE_DESCRIPTION]" label='('|cat:('Meta'|gettext)|cat:') '|cat:('Description'|gettext) value=$smarty.const.SITE_DESCRIPTION}
                </div>
                <div id="tab2">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("anti-spam measure settings"|gettext) module="anti-spam-measures"}
                        </div>
		                <h2>{"Anti-Spam Measures"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[SITE_USE_ANTI_SPAM]" label="Use Anti-Spam measures?"|gettext checked=$smarty.const.SITE_USE_ANTI_SPAM value=1}
                    {control type="checkbox" postfalse=1 name="sc[ANTI_SPAM_USERS_SKIP]" label="Skip using Anti-Spam measures for Logged-In Users?"|gettext checked=$smarty.const.ANTI_SPAM_USERS_SKIP value=1}
                    {control type="dropdown" name="sc[ANTI_SPAM_CONTROL]" label="Anti-Spam Method"|gettext items=$as_types default=$smarty.const.ANTI_SPAM_CONTROL}
	                <p>{'To obtain the reCAPTCHA \'keys\', you\'ll need to first have a'|gettext} <a href="http://www.google.com/" target="_blank">{"Google account"|gettext}</a> {"to log in, then setup up a reCAPTCHA account for your domain(s)"|gettext} <a href="http://www.google.com/recaptcha/whyrecaptcha" target="_blank">{"here"|gettext}</a></p>
                    {control type="dropdown" name="sc[RECAPTCHA_THEME]" label="re-Captcha Theme"|gettext items=$as_themes default=$smarty.const.RECAPTCHA_THEME}
                    {control type="text" name="sc[RECAPTCHA_PUB_KEY]" label="reCAPTCHA Public Key"|gettext value=$smarty.const.RECAPTCHA_PUB_KEY}
                    {control type="text" name="sc[RECAPTCHA_PRIVATE_KEY]" label="reCAPTCHA Private Key"|gettext value=$smarty.const.RECAPTCHA_PRIVATE_KEY}
                </div>
                <div id="tab3">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("user registration settings"|gettext) module="user-registration"}
                        </div>
		                <h2>{"User Registration"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[SITE_ALLOW_REGISTRATION]" label="Should users be allowed to create accounts for themselves?"|gettext checked=$smarty.const.SITE_ALLOW_REGISTRATION value=1}
                    {control type="checkbox" postfalse=1 name="sc[USER_REGISTRATION_USE_EMAIL]" label="Use an email address instead of a username?"|gettext checked=$smarty.const.USER_REGISTRATION_USE_EMAIL value=1}
                    {control type="checkbox" postfalse=1 name="sc[USER_REGISTRATION_SEND_NOTIF]" label="Notify a site administrator by email when a new user registers on your website?"|gettext checked=$smarty.const.USER_REGISTRATION_SEND_NOTIF value=1}
                    {control type="text" name="sc[USER_REGISTRATION_NOTIF_SUBJECT]" label='Subject of the administrator\'s new user notification'|gettext value=$smarty.const.USER_REGISTRATION_NOTIF_SUBJECT}
                    {control type="text" name="sc[USER_REGISTRATION_ADMIN_EMAIL]" label="Email address of administrator that should be notified when a user signs up"|gettext value=$smarty.const.USER_REGISTRATION_ADMIN_EMAIL}
                    {control type="checkbox" postfalse=1 name="sc[USER_REGISTRATION_SEND_WELCOME]" label="Send a Welcome email to the user after signing up?"|gettext checked=$smarty.const.USER_REGISTRATION_SEND_WELCOME value=1}
                    {control type="text" name="sc[USER_REGISTRATION_WELCOME_SUBJECT]" label="Subject of the Welcome email to the user"|gettext value=$smarty.const.USER_REGISTRATION_WELCOME_SUBJECT}
                    {control type="textarea" name="sc[USER_REGISTRATION_WELCOME_MSG]" label="Content of email sent to the user upon completing registration"|gettext value=$smarty.const.USER_REGISTRATION_WELCOME_MSG}
                </div>
                <div id="tab4">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("user comment policy settings"|gettext) module="user-comment-policies"}
                        </div>
		                <h2>{"User Comment Policies"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[COMMENTS_REQUIRE_LOGIN]" label="Require User Login to Post Comments?"|gettext checked=$smarty.const.COMMENTS_REQUIRE_LOGIN value=1}
                    {control type="checkbox" postfalse=1 name="sc[COMMENTS_REQUIRE_APPROVAL]" label="All Comments Must be Approved?"|gettext checked=$smarty.const.COMMENTS_REQUIRE_APPROVAL value=1}
                    {control type="checkbox" postfalse=1 name="sc[COMMENTS_REQUIRE_NOTIFICATION]" label="Notify a site administrator of New Comments?"|gettext checked=$smarty.const.COMMENTS_REQUIRE_NOTIFICATION value=1}
                    {control type="text" name="sc[COMMENTS_NOTIFICATION_EMAIL]" label="Email address(es) that should be notified of New Comments"|gettext|cat:' <br />('|cat:("Enter multiple addresses by using a comma to separate them"|gettext)|cat:')' value=$smarty.const.COMMENTS_NOTIFICATION_EMAIL}
                </div>
                <div id="tab5">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("display settings"|gettext) module="display-settings"}
                        </div>
		                <h2>{"Display Settings"|gettext}</h2>
                    </div>
                    {control type="dropdown" name="sc[LANGUAGE]" label="Display Language"|gettext items=$langs default=$smarty.const.LANGUAGE}
                    {*{control type="dropdown" name="sc[DISPLAY_THEME_REAL]" label="Theme <a href=\"manage_themes\">(More Theme Options)</a>"|gettext items=$themes default=$smarty.const.DISPLAY_THEME_REAL}*}
	                <h3><a href=manage_themes>Display Theme Options</a></h3>
	                {control type="checkbox" postfalse=1 name="sc[FORCE_MOBILE]" label="Force Display of the Mobile Theme Variation (if available)?"|gettext checked=$smarty.const.FORCE_MOBILE value=1}
                    {control type="dropdown" name="sc[DISPLAY_ATTRIBUTION]" label="Attribution Display"|gettext items=$attribution default=$smarty.const.DISPLAY_ATTRIBUTION}
	                {control type="dropdown" name="sc[DISPLAY_DATETIME_FORMAT]" label="Date/Time Format"|gettext items=$datetime_format default=$smarty.const.DISPLAY_DATETIME_FORMAT}
                    {control type="dropdown" name="sc[DISPLAY_DATE_FORMAT]" label="Date Format"|gettext items=$date_format default=$smarty.const.DISPLAY_DATE_FORMAT}
                    {control type="dropdown" name="sc[DISPLAY_TIME_FORMAT]" label="Time Format"|gettext items=$time_format default=$smarty.const.DISPLAY_TIME_FORMAT}
                    {control type="dropdown" name="sc[DISPLAY_START_OF_WEEK]" label="Start of Week"|gettext items=$start_of_week default=$smarty.const.DISPLAY_START_OF_WEEK}
	                {control type="dropdown" name="sc[DISPLAY_DEFAULT_TIMEZONE]" label="Default timezone for this site"|gettext|cat:(' <br />'|cat:("CAUTION: This may break calendars and other features that use date functions if you change this after entering data."|gettext)) items=$timezones default=$smarty.const.DISPLAY_DEFAULT_TIMEZONE}
                    {control type="radiogroup" name="sc[SLINGBAR_TOP]" label="Default Admin Slingbar Position" items="Top of Viewport,Bottom of Viewport" values="1,0" default=$smarty.const.SLINGBAR_TOP}
					{control type="text" name="sc[THUMB_QUALITY]" label="Thumbnail JPEG Quality"|gettext|cat:" (0 - 95)" value=$smarty.const.THUMB_QUALITY|default:75 size="5"}
                </div>
                {if $user->is_admin==1}
                <div id="tab6">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("mail server settings"|gettext) module="mail-server-settings"}
                        </div>
		                <h2>{"Mail Server Settings"|gettext}</h2>
                    </div>
	                {control type="text" name="sc[SMTP_FROMADDRESS]" label="From Address"|gettext value=$smarty.const.SMTP_FROMADDRESS}
                    {br}{control type="checkbox" postfalse=1 name="sc[SMTP_USE_PHP_MAIL]" label='Use php\'s mail() function instead of SMTP?'|gettext checked=$smarty.const.SMTP_USE_PHP_MAIL value=1}
	                (or)<h3>{"SMTP Server Settings"|gettext}</h3>
                    {control type="text" name="sc[SMTP_SERVER]" label="SMTP Server"|gettext value=$smarty.const.SMTP_SERVER}
                    {control type="text" name="sc[SMTP_PORT]" label="SMTP Port"|gettext value=$smarty.const.SMTP_PORT}
                    {control type="dropdown" name="sc[SMTP_PROTOCOL]" label="Type of Encrypted Connection"|gettext items=$protocol default=$smarty.const.SMTP_PROTOCOL includeblank="None"}
                    {control type="text" name="sc[SMTP_USERNAME]" label="SMTP Username"|gettext value=$smarty.const.SMTP_USERNAME}
                    {control type="text" name="sc[SMTP_PASSWORD]" label="SMTP Password"|gettext value=$smarty.const.SMTP_PASSWORD}
	                {control type="checkbox" postfalse=1 name="sc[SMTP_DEBUGGING]" label="Turn SMTP Debugging On?"|gettext checked=$smarty.const.SMTP_DEBUGGING value=1}
                </div>
                <div id="tab7">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("site maintenance mode settings"|gettext) module="site-maintenance-mode-settings"}
                        </div>
		                <h2>{"Site Maintenance Mode Settings"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[MAINTENANCE_MODE]" label="Enter Maintenance Mode?"|gettext checked=$smarty.const.MAINTENANCE_MODE value=1}
                    {control type="html" name="sc[MAINTENANCE_MSG_HTML]" label="Maintenance Mode Message"|gettext value=$smarty.const.MAINTENANCE_MSG_HTML}
                </div>
                <div id="tab8">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("security settings"|gettext) module="security-settings"}
                        </div>
		                <h2>{"Security Settings"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[SESSION_TIMEOUT_ENABLE]" label="Enable Session Timeout?"|gettext checked=$smarty.const.SESSION_TIMEOUT_ENABLE value=1}
                    {control type="text" name="sc[SESSION_TIMEOUT]" label="Session Timeout in seconds"|gettext value=$smarty.const.SESSION_TIMEOUT}
                    {control type="dropdown" name="sc[FILE_DEFAULT_MODE_STR]" label="Default File Permissions"|gettext items=$file_permisions default=$smarty.const.FILE_DEFAULT_MODE_STR}
                    {control type="dropdown" name="sc[DIR_DEFAULT_MODE_STR]" label="Default Directory Permissions"|gettext items=$dir_permissions default=$smarty.const.DIR_DEFAULT_MODE_STR}
                    {control type="checkbox" postfalse=1 name="sc[ENABLE_SSL]" label="Enable SSL Support?"|gettext checked=$smarty.const.ENABLE_SSL value=1}
                    {control type="text" name="sc[NONSSL_URL]" label="Non-SSL URL Base"|gettext value=$smarty.const.NONSSL_URL}
                    {control type="text" name="sc[SSL_URL]" label="SSL URL Base"|gettext value=$smarty.const.SSL_URL}
                </div>
                <div id="tab9">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("help link settings"|gettext) module="help-link-settings"}
                        </div>
		                <h2>{"Help Link Settings"|gettext}</h2>
                    </div>
                    {control type="checkbox" postfalse=1 name="sc[HELP_ACTIVE]" label="Enable Help links for documentation?"|gettext checked=$smarty.const.HELP_ACTIVE value=1}
                    {control type="text" name="sc[HELP_URL]" label="URL for Help Documentation"|gettext value=$smarty.const.HELP_URL}
                </div>
                <div id="tab10">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("WYSIWYG Editor Settings"|gettext) module="wysiwyg-editor-settings"}
                        </div>
		                <h2>{"WYSIWYG Editor Settings"|gettext}</h2>
                    </div>
                    {control type="dropdown" name="sc[SITE_WYSIWYG_EDITOR]" label="HTML Editor"|gettext items="CKEditor" values="ckeditor" default=$smarty.const.SITE_WYSIWYG_EDITOR}
	                {if $smarty.const.SITE_WYSIWYG_EDITOR == 'ckeditor'}
						{br}<hr>
		                {chain module=expHTMLEditor view=manage}
	                {/if}
                </div>
                <div id="tab11">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("error message settings"|gettext) module="error-messages"}
                        </div>
		                <h2>{"Error Messages"|gettext}</h2>
                    </div>
                    {control type="text" name="sc[SITE_404_TITLE]" label='Page Title For \'Not Found\' (404) Error'|gettext value=$smarty.const.SITE_404_TITLE}
                    {control type="html" name="sc[SITE_404_HTML]" label='\'Not Found\' (404) Error Message'|gettext value=$smarty.const.SITE_404_HTML}
                    {control type="html" name="sc[SITE_403_REAL_HTML]" label='\'Access Denied\' (403) Error Message'|gettext value=$smarty.const.SITE_403_REAL_HTML}
                    {control type="html" name="sc[SESSION_TIMEOUT_HTML]" label='\'Session Expired\' Error  Message'|gettext value=$smarty.const.SESSION_TIMEOUT_HTML}
                </div>
                <div id="tab12">
	                <div class="info-header">
                        <div class="related-actions">
	                        {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("generating PDF settings"|gettext) module="pdf-generation"}
                        </div>
		                <h2>{"PDF Generation"|gettext}</h2>
                    </div>
                    {control type="text" name="sc[HTMLTOPDF_PATH]" label="Full Path to the WKHTMLtoPDF Binary Utility"|gettext value=$smarty.const.HTMLTOPDF_PATH}
                    {control type="text" name="sc[HTMLTOPDF_PATH_TMP]" label="Full Path to the WKHTMLtoPDF Temp Directory"|gettext value=$smarty.const.HTMLTOPDF_PATH_TMP}
                </div>
				<div id="tab13">
					<div class="info-header">
			            <div class="related-actions">
				            {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("minification settings"|gettext) module="minify-configuration"}
			            </div>
			            <h2>{"Minify Configuration"|gettext}</h2>
			        </div>
                    {control type="text" name="sc[MINIFY_MAXAGE]" label="Maximum age of browser cache in seconds"|gettext value=$smarty.const.MINIFY_MAXAGE}
					{control type="text" name="sc[MINIFY_MAX_FILES]" label='Maximum # of files that can be specified in the \'f\' GET parameter'|gettext value=$smarty.const.MINIFY_MAX_FILES}
					{control type="text" name="sc[MINIFY_URL_LENGTH]" label="The length of minification url"|gettext value=$smarty.const.MINIFY_URL_LENGTH}
					{control type="checkbox" postfalse=1 name="sc[MINIFY_ERROR_LOGGER]" label="Enable logging of minify error messages to FirePHP?"|gettext checked=$smarty.const.MINIFY_ERROR_LOGGER value=1}
                </div>
				
				<div id="tab14">
					 <h2>{"Search Report Configuration"|gettext}</h2>
					 
					{control type="text" name="sc[TOP_SEARCH]" label="Number of Top Search Queries"|gettext value=$smarty.const.TOP_SEARCH}
					{control type="checkbox" postfalse=1 name="sc[INCLUDE_AJAX_SEARCH]" label="Include ajax search in reports?"|gettext checked=$smarty.const.INCLUDE_AJAX_SEARCH value=1}
					{control type="checkbox" postfalse=1 name="sc[INCLUDE_ANONYMOUS_SEARCH]" label="Include unregistered users search?"|gettext checked=$smarty.const.INCLUDE_ANONYMOUS_SEARCH value=1}
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
	YUI(EXPONENT.YUI3_CONFIG).use('history','tabview', function(Y) {
		var history = new Y.HistoryHash(),
	        tabview = new Y.TabView({srcNode:'#{/literal}{$config}{literal}'});
	    tabview.render();
	    Y.one('#{/literal}{$config}{literal}').removeClass('hide');
	    Y.one('.loadingdiv').remove();

		// Set the selected tab to the bookmarked history state, or to
		// the first tab if there's no bookmarked state.
		tabview.selectChild(history.get('tab') || 0);

		// Store a new history state when the user selects a tab.
		tabview.after('selectionChange', function (e) {
		  // If the new tab index is greater than 0, set the "tab"
		  // state value to the index. Otherwise, remove the "tab"
		  // state value by setting it to null (this reverts to the
		  // default state of selecting the first tab).
		  history.addValue('tab', e.newVal.get('index') || null);
		});

		// Listen for history changes from back/forward navigation or
		// URL changes, and update the tab selection when necessary.
		Y.on('history:change', function (e) {
		  // Ignore changes we make ourselves, since we don't need
		  // to update the selection state for those. We're only
		  // interested in outside changes, such as the ones generated
		  // when the user clicks the browser's back or forward buttons.
		  if (e.src === Y.HistoryHash.SRC_HASH) {

		    if (e.changed.tab) {
		      // The new state contains a different tab selection, so
		      // change the selected tab.
		      tabview.selectChild(e.changed.tab.newVal);
		    } else if (e.removed.tab) {
		      // The tab selection was removed in the new state, so
		      // select the first tab by default.
		      tabview.selectChild(0);
		    }
		  }
		});
	});
{/literal}
{/script}
