{*
 * Copyright (c) 2004-2022 OIC Group, Inc.
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

<div class="form_header">
	<div class="info-header">
		<div class="related-actions">
		    {help text="Get Help with"|gettext|cat:" "|cat:("Facebook Settings"|gettext) module="facebook-button"}
		</div>
        <h2>{'Facebook Settings'|gettext}</h2>
	</div>
</div>
{group label='Facebook Meta Tags'|gettext}
    {control type="checkbox" name="disable_facebook_meta" label="Disable Facebook Meta Tags"|gettext value=1 checked=$config.disable_facebook_meta description='Disables \'Facebook og:xxx\' meta tags on page'|gettext}
    {control type="files" name="fbimage" subtype=fbimage label="Default Meta Image"|gettext value=$config.expFile folder=$config.upload_folder limit=1 description='Module Default Image for social media (1200px x 630px or 600px x 315px, but larger than 200px x 200px)'|gettext}
{/group}
{control type="text" name="app_id" label="App ID"|gettext value=$config.app_id class=title description='Facebook App ID is needed for Status Posting and Like Buttons'|gettext}
{group label='Auto Facebook Status Posting'|gettext}
    {control type="checkbox" name="enable_auto_status" label="Enable Auto-Facebook Status"|gettext value=1 checked=$config.enable_auto_status description='Allows \'Facebook\'ing new items'|gettext}
    {group label='Facebook Account'|gettext}
        {control type="text" name="facebook_page" label="Facebook Page"|gettext value=$config.facebook_page placeholder='john.smith.666'}
        <blockquote>
            {'Log in to the Facebook, then visit the Developer\'s create app page'|gettext} <a href="http://developers.facebook.com/setup/" target="_blank">{'website'|gettext}</a>{br}
            {'First create a new app which will provide you the App ID and App Secret.'|gettext}{br}
        </blockquote>
        {control type="text" name="app_secret" label="App secret"|gettext value=$config.app_secret class=title}
        {'Save these settings, then'|gettext} <a href="http://www.facebook.com/dialog/oauth?app_id={$config.app_id}&redirect_uri={urlencode($smarty.const.URL_FULL)}&scope=user_posts,user_events,user_photos,user_videos,manage_pages,publish_pages" target="_blank">{'Establish Facebook Permissions'|gettext}</a>
    {/group}
{/group}
{group label='Facebook Like Button'|gettext}
    {control type="checkbox" name="enable_facebook_like" label="Enable Facebook Like Button"|gettext value=1 checked=$config.enable_facebook_like description='Displays the \'Like\' button with each item'|gettext}
    {control type="dropdown" name="fblayout" items="Standard,Button Count,Box Count,Button"|gettxtlist values="standard,button_count,box_count,button" label="Layout Style"|gettext value=$config.fblayout}
    {control type="text" name="fblwidth" label="Width"|gettext filter=integer size=3 value=$config.fblwidth}
{*    {control type="checkbox" name="showfaces" label="Show Faces"|gettext value=1 checked=$config.showfaces}*}
{*    {control type="dropdown" name="font" items="Arial,Lucida Grande,Segoe UI,Tahoma,Trebuchet MS,Verdana" values="arial,lucida grande,segoe ui,tahoma,trebuchet ms,verdana" label="Font"|gettext value=$config.font|default:""}*}
{*    {control type="dropdown" name="color_scheme" items="Light,Dark"|gettxtlist values=",dark" label="Color Scheme"|gettext value=$config.color_scheme|default:""}*}
    {control type="dropdown" name="fblsize" items="Small,Large"|gettxtlist values="small,large" label="Button Size"|gettext value=$config.fblsize|default:"small"}
    {control type="dropdown" name="fbverb" items="Like,Recommend"|gettxtlist values="like,recommend" label="Verb to Display"|gettext value=$config.fbverb|default:""}
{/group}
