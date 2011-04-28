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

<div id="siteconfig" class="module administration configure-site exp-skin-tabview hide">
    
    <h1>Configure Website</h1>
    
    {script unique="siteconfig" yui2mods="tabview, element" yui3mods=1}
    {literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node', function(Y) {
        var tabView = new YAHOO.widget.TabView('demo');
        Y.one('#siteconfig').removeClass('hide');
        Y.one('.loadingdiv').remove();
    });
    {/literal}
    {/script}

    {help text="Learn More about configuring your website"|gettext page="exponent-cms-configuration-documentation"}
    
    {form controller="administration" action=update_siteconfig}
        <div id="demo" class="yui-navset">
            <ul class="yui-nav">
            <li class="selected"><a href="#tab1"><em>{gettext str="General Site Configuration"}</em></a></li>
            <li><a href="#tab2"><em>{gettext str="Anti-Spam Measures"}</em></a></li>
            <li><a href="#tab3"><em>{gettext str="User Registration"}</em></a></li>
            <li><a href="#tab4"><em>{gettext str="User Comment Policies"}</em></a></li>
            <li><a href="#tab5"><em>{gettext str="Display Settings"}</em></a></li>
            {if $user->is_admin==1}
            <li><a href="#tab6"><em>{gettext str="Mail Settings"}</em></a></li>
            <li><a href="#tab7"><em>{gettext str="Maintenance Settings"}</em></a></li>
            <li><a href="#tab8"><em>{gettext str="Security Settings"}</em></a></li>
            <li><a href="#tab9"><em>{gettext str="Help Settings"}</em></a></li>
            <li><a href="#tab10"><em>{gettext str="HTML Editor Settings"}</em></a></li>
            <li><a href="#tab10"><em>{gettext str="Error Messages"}</em></a></li>
            {/if}
            </ul>            
            <div class="yui-content">
                <div id="tab1">
                    <h2>{"General Site Configuration"|gettext}</h2>
                    {control type="text" name="sc[ORGANIZATION_NAME]" label="Organization Name" value=$smarty.const.ORGANIZATION_NAME}
                    {control type="text" name="sc[SITE_TITLE]" label="Site Title" value=$smarty.const.SITE_TITLE}
                    {control type="checkbox" postfalse=1 name="sc[SEF_URLS]" label="Search Engine Friendly URLSs" checked=$smarty.const.SEF_URLS value=1}
                    {control type="dropdown" name="sc[SITE_DEFAULT_SECTION]" label="Default Section (Home Page)" items=$section_dropdown default=$smarty.const.SITE_DEFAULT_SECTION}
                    {control type="textarea" name="sc[SITE_KEYWORDS]" label="Meta Keywords" value=$smarty.const.SITE_KEYWORDS}
                    {control type="textarea" name="sc[SITE_DESCRIPTION]" label="Meta Description" value=$smarty.const.SITE_DESCRIPTION}
                </div>
                <div id="tab2">
					<h2>{gettext str="Anti-Spam Measures"}</h2>
                    {control type="checkbox" postfalse=1 name="sc[SITE_USE_ANTI_SPAM]" label="Use Anti-Spam measures?" checked=$smarty.const.SITE_USE_ANTI_SPAM value=1}
                    {control type="checkbox" postfalse=1 name="sc[ANTI_SPAM_USERS_SKIP]" label="Skip using Anti-Spam measures for Logged-In Users?" checked=$smarty.const.ANTI_SPAM_USERS_SKIP value=1}
                    {control type="dropdown" name="sc[ANTI_SPAM_CONTROL]" label="Anti-Spam Method" items=$as_types default=$smarty.const.ANTI_SPAM_CONTROL}
                    {control type="dropdown" name="sc[RECAPTCHA_THEME]" label="re-Captcha Theme" items=$as_themes default=$smarty.const.RECAPTCHA_THEME}
                    {control type="text" name="sc[RECAPTCHA_PUB_KEY]" label="reCAPTCHA Public Key" value=$smarty.const.RECAPTCHA_PUB_KEY}
                    {control type="text" name="sc[RECAPTCHA_PRIVATE_KEY]" label="reCAPTCHA Private Key" value=$smarty.const.RECAPTCHA_PRIVATE_KEY}
                </div>
                <div id="tab3">
					<h2>{gettext str="User Registration"}</h2>
                    {control type="checkbox" postfalse=1 name="sc[SITE_ALLOW_REGISTRATION]" label="Should users be allowed to create accounts for themselves?" checked=$smarty.const.SITE_ALLOW_REGISTRATION value=1}
                    {control type="checkbox" postfalse=1 name="sc[USER_REGISTRATION_USE_EMAIL]" label="Use an email address instead of a username?" checked=$smarty.const.USER_REGISTRATION_USE_EMAIL value=1}
                    {control type="checkbox" postfalse=1 name="sc[USER_REGISTRATION_SEND_NOTIF]" label="Send an email to a site administrator when a new user registers on your website?" checked=$smarty.const.USER_REGISTRATION_SEND_NOTIF value=1}
                    {control type="text" name="sc[USER_REGISTRATION_NOTIF_SUBJECT]" label="Administrator's New User notification subject" value=$smarty.const.USER_REGISTRATION_NOTIF_SUBJECT}
                    {control type="text" name="sc[USER_REGISTRATION_ADMIN_EMAIL]" label="The email address that should be notified when a user signs up" value=$smarty.const.USER_REGISTRATION_ADMIN_EMAIL}
                    {control type="checkbox" postfalse=1 name="sc[USER_REGISTRATION_SEND_WELCOME]" label="Send an welcome email to the user after signing up?" checked=$smarty.const.USER_REGISTRATION_SEND_WELCOME value=1}
                    {control type="text" name="sc[USER_REGISTRATION_WELCOME_SUBJECT]" label="The subject of the Welcome Email to the user" value=$smarty.const.USER_REGISTRATION_WELCOME_SUBJECT}
                    {control type="textarea" name="sc[USER_REGISTRATION_WELCOME_MSG]" label="The content of the email sent to the user upon completing registration" value=$smarty.const.USER_REGISTRATION_WELCOME_MSG}
                </div>
                <div id="tab4">
					<h2>{gettext str="User Comment Policies"}</h2>
                    {control type="checkbox" postfalse=1 name="sc[COMMENTS_REQUIRE_LOGIN]" label="Require Login to Post Comments" checked=$smarty.const.COMMENTS_REQUIRE_LOGIN value=1}
                    {control type="checkbox" postfalse=1 name="sc[COMMENTS_REQUIRE_APPROVAL]" label="I Want to Approve All Comments" checked=$smarty.const.COMMENTS_REQUIRE_APPROVAL value=1}
                    {control type="checkbox" postfalse=1 name="sc[COMMENTS_REQUIRE_NOTIFICATION]" label="Notify Me of New Comments" checked=$smarty.const.COMMENTS_REQUIRE_NOTIFICATION value=1}
                    {control type="text" name="sc[COMMENTS_NOTIFICATION_EMAIL]" label="Notification Email Address(es) (Enter multiple addresses by using a comma to separate them)" value=$smarty.const.COMMENTS_NOTIFICATION_EMAIL}
                </div>
                <div id="tab5">
					<h2>{gettext str="Display Settings"}</h2>
                    {control type="dropdown" name="sc[LANGUAGE]" label="Language" items=$langs default=$smarty.const.LANGUAGE}
                    {control type="dropdown" name="sc[DISPLAY_THEME_REAL]" label="Theme" items=$themes default=$smarty.const.DISPLAY_THEME_REAL}
                    {control type="dropdown" name="sc[DISPLAY_ATTRIBUTION]" label="Attribution Display" items=$attribution default=$smarty.const.DISPLAY_ATTRIBUTION}
                    {control type="dropdown" name="sc[DISPLAY_DATE_FORMAT]" label="Date Format" items=$date_format default=$smarty.const.DISPLAY_DATE_FORMAT}
                    {control type="dropdown" name="sc[DISPLAY_TIME_FORMAT]" label="Time Format" items=$time_format default=$smarty.const.DISPLAY_TIME_FORMAT}
                    {control type="dropdown" name="sc[DISPLAY_START_OF_WEEK]" label="Start of Week" items=$start_of_week default=$smarty.const.DISPLAY_START_OF_WEEK}
                    {control type="text" name="sc[DISPLAY_DEFAULT_TIMEZONE]" label="Enter the default timezone for this site. CAUTION: This may break calendars and other features that use date functions if you change this after entering data. Must be in a format shown here: <a href='http://www.php.net/manual/en/timezones.php' target='_blank'>http://www.php.net/manual/en/timezones.php</a>" value=$smarty.const.DISPLAY_DEFAULT_TIMEZONE}
                    {control type="radiogroup" name="sc[SLINGBAR_TOP]" label="Default Admin Slingbar Position" items="Top of Viewport,Bottom of Viewport" values="1,0" default=$smarty.const.SLINGBAR_TOP}
                    {control type="dropdown" name="sc[BTN_COLOR]" label="Form Button Color" items="Black,Green,Blue,Red,Magenta,Orange,Yellow,Grey" values="black,green,blue,red,magenta,orange,yellow,grey" default=$smarty.const.BTN_COLOR}
                    {control type="dropdown" name="sc[BTN_SIZE]" label="Form Button Size" items="Large,Medium,Small" values="large,medium,small" default=$smarty.const.BTN_SIZE}
                </div>
                {if $user->is_admin==1}
                <div id="tab6">
                    <h2>{gettext str="SMTP Mail Settings"}</h2>
                    {control type="checkbox" postfalse=1 name="sc[SMTP_USE_PHP_MAIL]" label="Use php's mail() function instead of SMTP?" checked=$smarty.const.SMTP_USE_PHP_MAIL value=1}
                    {control type="text" name="sc[SMTP_SERVER]" label="SMTP Server" value=$smarty.const.SMTP_SERVER}
                    {control type="text" name="sc[SMTP_PORT]" label="SMTP Port" value=$smarty.const.SMTP_PORT}
                    {control type="dropdown" name="sc[SMTP_AUTHTYPE]" label="Authentication Method" items="NONE,LOGIN,PLAIN" default=$smarty.const.SMTP_AUTHTYPE includeblank="No Authentication"}
                    {control type="text" name="sc[SMTP_USERNAME]" label="SMTP Username" value=$smarty.const.SMTP_USERNAME}
                    {control type="text" name="sc[SMTP_PASSWORD]" label="SMTP Password" value=$smarty.const.SMTP_PASSWORD}
                    {control type="text" name="sc[SMTP_FROMADDRESS]" label="From Address" value=$smarty.const.SMTP_FROMADDRESS}
                </div>
                <div id="tab7">
                    <h2>{gettext str="Site Maintenance Mode Settings"}</h2>
                    {control type="checkbox" postfalse=1 name="sc[MAINTENANCE_MODE]" label="Maintenance Mode" checked=$smarty.const.MAINTENANCE_MODE value=1}
                    {control type="html" name="sc[MAINTENANCE_MSG_HTML]" label="Maintenance Mode Message" value=$smarty.const.MAINTENANCE_MSG_HTML}
                </div>
                <div id="tab8">
                    <h2>{gettext str="Security Settings"}</h2>
                    {control type="checkbox" postfalse=1 name="sc[SESSION_TIMEOUT_ENABLE]" label="Enable Session Timeout" checked=$smarty.const.SESSION_TIMEOUT_ENABLE value=1}
                    {control type="text" name="sc[SESSION_TIMEOUT]" label="Session Timeout in seconds" value=$smarty.const.SESSION_TIMEOUT}
                    {control type="dropdown" name="sc[FILE_DEFAULT_MODE_STR]" label="Default File Permissions" items=$file_permisions default=$smarty.const.FILE_DEFAULT_MODE_STR}
                    {control type="dropdown" name="sc[DIR_DEFAULT_MODE_STR]" label="Default Directory Permissions" items=$dir_permissions default=$smarty.const.DIR_DEFAULT_MODE_STR}
                    {control type="checkbox" postfalse=1 name="sc[ENABLE_SSL]" label="Enable SSL Support" checked=$smarty.const.ENABLE_SSL value=1}
                    {control type="text" name="sc[NONSSL_URL]" label="Non-SSL URL Base" value=$smarty.const.NONSSL_URL}
                    {control type="text" name="sc[SSL_URL]" label="SSL URL Base" value=$smarty.const.SSL_URL}
                </div>
                <div id="tab9">
                    <h2>{gettext str="Help Link Settings"}</h2>
                    {control type="checkbox" postfalse=1 name="sc[HELP_ACTIVE]" label="Enable Help links for documentation?" checked=$smarty.const.HELP_ACTIVE value=1}
                    {control type="text" name="sc[HELP_URL]" label="URL For Help Documentation" value=$smarty.const.HELP_URL}
                </div>
                <div id="tab10">
                    <h2>{gettext str="HTML Editor Settings"}</h2>
                    {control type="dropdown" name="sc[SITE_WYSIWYG_EDITOR]" label="HTML Editor" items="CKEditor,FCK Editor" values="ckeditor,FCKeditor" default=$smarty.const.SITE_WYSIWYG_EDITOR}
                </div>
                <div id="tab11">
                    <h2>{gettext str="Error Messages"}</h2>
                    {control type="text" name="sc[SITE_404_TITLE]" label="Page Title For 'Not Found' (404) Error" value=$smarty.const.SITE_404_TITLE}
                    {control type="html" name="sc[SITE_404_HTML]" label="'Not Found' (404) Error Message" value=$smarty.const.SITE_404_HTML}
                    {control type="html" name="sc[SITE_403_REAL_HTML]" label="'Access Denied' (403) Error Message" value=$smarty.const.SITE_403_REAL_HTML}
                    {control type="html" name="sc[SESSION_TIMEOUT_HTML]" label="'Session Expired' Error  Message" value=$smarty.const.SESSION_TIMEOUT_HTML}
                </div>
                {/if}
            </div>
        </div>
        {control type="buttongroup" submit="Save Website Configuration" cancel="Cancel"}
    {/form}
</div>
<div class="loadingdiv">Loading</div>
