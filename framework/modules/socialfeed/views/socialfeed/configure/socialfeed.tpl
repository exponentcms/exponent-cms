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

<div class="form_header">
	<div class="info-header">
		<div class="related-actions">
		    {help text="Get Help with"|gettext|cat:" "|cat:("Social Feed Settings"|gettext) module="socialfeed"}
		</div>
        <h2>{"Social Feed Settings"|gettext}</h2>
        <blockquote>
          {'The Social Feed module allows you to fetch feeds directly from Facebook, Twitter & Instagram and aggregate them.'|gettext}
        </blockquote>
	</div>
</div>

{control type=text name='socialfeed_feeds_count' label='Number of Feed Items'|gettext size=60 max=100 filter=integer value=$config.socialfeed_feeds_count|default:3}
{control type=text name='socialfeed_cache_refresh' label='Feed cache refresh time limit'|gettext size=60 max=60 filter=integer value=$config.socialfeed_cache_refresh|default:120 description='How often to update social feed cache in minutes'|gettext}
{control type=text name='socialfeed_trim_length' label='Trim Description Length'|gettext size=60 max=60 filter=integer value=$config.socialfeed_trim_length|default:120 description='Maximum length of descriptions'|gettext}
{control type=checkbox name="socialfeed_time_stamp" value=1 label="Show Posted Date/Time"|gettext checked=$config.socialfeed_time_stamp|default:false}
{control type=dropdown name="socialfeed_display_type" items="Collapsing Columns,Fluid"|gettxtlist values="columns,fluid" label="Bootstrap 3 display style"|gettext value=$config.socialfeed_display_type|default:'fluid'}
{control type=checkbox name="socialfeed_facebook_use" value=1 label="Get Facebook Feed"|gettext checked=$config.socialfeed_facebook_use}
{group id="facebook" label='Facebook Settings'|gettext}
  {control type=text name='socialfeed_facebook_page_name' label='Facebook Page Name'|gettext size=60 max=100 value=$config.socialfeed_facebook_page_name description='eg. If your Facebook page URL is this http://www.facebook.com/YOUR_PAGE_NAME, <br />then you just need to add this YOUR_PAGE_NAME above.'|gettext}
  {control type=checkbox name="socialfeed_facebook_apiv24" value=1 postfalse=1 label="Is Facebook App API version 2.4 or newer?"|gettext checked=$config.socialfeed_facebook_apiv24}
  {control type=text name='socialfeed_facebook_app_id' label='App ID'|gettext size=60 max=100 value=$config.socialfeed_facebook_app_id}
  {control type=text name='socialfeed_facebook_secret_key' label='Secret Key'|gettext size=60 max=100 value=$config.socialfeed_facebook_secret_key}
    {group label='Post Types to Display'|gettext}
      {*{control type=checkbox name="socialfeed_facebook_all_types" value=1 postfalse=1 label="Show all post types"|gettext checked=$config.socialfeed_facebook_all_types|default:true}*}
      {*{control type=dropdown name="socialfeed_facebook_post_type" items="Link,Status,Photo,Video"|gettxtlist values="link,status,photo,video" includeblank='- Select -'|gettext label="(or) Select your post type(s) to show"|gettext value=$config.socialfeed_facebook_post_type}*}
      {control type=checkbox name="socialfeed_facebook_post_type[status]" value=1 postfalse=1 label="Show status posts"|gettext checked=$config.socialfeed_facebook_post_type[status]|default:true}
      {control type=checkbox name="socialfeed_facebook_post_type[photo]" value=1 postfalse=1 label="Show photo posts"|gettext checked=$config.socialfeed_facebook_post_type[photo]|default:true}
      {control type=checkbox name="socialfeed_facebook_post_type[video]" value=1 postfalse=1 label="Show video posts"|gettext checked=$config.socialfeed_facebook_post_type[video]|default:true}
      {control type=checkbox name="socialfeed_facebook_post_type[event]" value=1 postfalse=1 label="Show upcoming event posts"|gettext checked=$config.socialfeed_facebook_post_type[event]|default:true}
      {control type=checkbox name="socialfeed_facebook_post_type[link]" value=1 postfalse=1 label="Show link posts"|gettext checked=$config.socialfeed_facebook_post_type[link]|default:true}
    {/group}
    {group label='Post Format'|gettext}
      {control type=checkbox name="socialfeed_facebook_display_pic" value=1 label="Show Post Picture"|gettext checked=$config.socialfeed_facebook_display_pic|default:false}
      {control type=checkbox name="socialfeed_facebook_display_video" value=1 label="Show Post Video"|gettext checked=$config.socialfeed_facebook_display_video|default:false}
      {control type=checkbox name="socialfeed_facebook_hashtag" value=1 label="Show Post Hashtag as Link"|gettext checked=$config.socialfeed_facebook_hashtag|default:false}
      {control type=text name='socialfeed_facebook_time_format' label='Date/Time format for Events'|gettext size=60 max=100 value=$config.socialfeed_facebook_time_format|default:'F j, Y @ g:i a' description='You can check for PHP Date Formats <a href="http://php.net/manual/en/function.date.php" target="_blank">here</a>'}
    {/group}
{/group}
{control type=checkbox name="socialfeed_twitter_use" value=1 label="Get Twitter Feed"|gettext checked=$config.socialfeed_twitter_use}
{group id="twitter" label='Twitter settings'|gettext}
  {control type=text name='socialfeed_twitter_username' label='Twitter User Name'|gettext size=60 max=100 value=$config.socialfeed_twitter_username}
  {control type=text name='socialfeed_twitter_access_token' label='Access token'|gettext size=60 max=100 value=$config.socialfeed_twitter_access_token}
  {control type=text name='socialfeed_twitter_access_token_secret' label='Access token secret'|gettext size=60 max=100 value=$config.socialfeed_twitter_access_token_secret}
  {control type=text name='socialfeed_twitter_consumer_key' label='Consumer Key'|gettext size=60 max=100 value=$config.socialfeed_twitter_consumer_key}
  {control type=text name='socialfeed_twitter_consumer_secret' label='Consumer Secret'|gettext size=60 max=100 value=$config.socialfeed_twitter_consumer_secret}
  {control type=checkbox name="socialfeed_twitter_hashtag" value=1 label="Show Tweet Hashtag as Link"|gettext checked=$config.socialfeed_twitter_hashtag|default:false}
{/group}
{control type=checkbox name="socialfeed_instagram_use" value=1 label="Get Instagram Feed"|gettext checked=$config.socialfeed_instagram_use}
{group id="instagram" label='Instagram settings'|gettext}
  {control type=text name='socialfeed_instagram_username' label='Instagram User Name'|gettext size=60 max=100 value=$config.socialfeed_instagram_username}
  {*{'To get Client ID you need to manage clients from your instagram account detailed information'|gettext}*}
  {*{control type=text name='socialfeed_instagram_client_id' label='Client ID'|gettext size=60 max=100 value=$config.socialfeed_instagram_client_id description='Client ID from Instagram account'|gettext}*}
  {*{'Generate Instagram Access Token'|gettext}*}
      {*'#description' => t('Access this URL in your browser https://instagram.com/oauth/authorize/?client_id=&lt;your_client_id&gt;&redirect_uri=&lt;your_redirect_uri&gt;&response_type=token, you will get the access token.'),*}
      {*'#markup' => t('Check ' . l('this', 'http://jelled.com/instagram/access-token'|gettext} article'),*}
  {*{control type=text name='socialfeed_instagram_access_token' label='Access Token'|gettext size=60 max=100 value=$config.socialfeed_instagram_access_token}*}
  {*{control type=text name='socialfeed_instagram_redirect_uri' label='Redirect URI'|gettext size=60 max=100 value=$config.socialfeed_instagram_redirect_uri description='Redirect URI from Instagram account'|gettext}*}
  {*{control type=text name='socialfeed_instagram_picture_count' label='Picture Count'|gettext size=60 max=100 filter=integer value=$config.socialfeed_instagram_picture_count|default:1}*}
  {*{'Feed URL'|gettext}*}
      {*'#markup' => t('https://api.instagram.com/v1/users/self/feed?access_token=' . $config.socialfeed_instagram_access_token &count=' . variable_get('socialfeed_instagram_picture_count', 1)),*}
  {control type=dropdown name="socialfeed_instagram_picture_resolution" items="Thumbnail,Low Resolution,Standard Resolution"|gettxtlist values="thumbnail,low_resolution,standard_resolution" include_blank=1 label="Picture Resolution"|gettext value=$config.socialfeed_instagram_picture_resolution|default:'standard'}
  {control type=checkbox name="socialfeed_instagram_post_link" value=1 label="Show Post URL"|gettext checked=$config.socialfeed_instagram_post_link|default:false}
{/group}
{control type=checkbox name="socialfeed_pinterest_use" value=1 label="Get Pinterest Feed"|gettext checked=$config.socialfeed_pinterest_use}
{group id="pinterest" label='Pinterest settings'|gettext}
  {control type=text name='socialfeed_pinterest_username' label='Pinterest User Name'|gettext size=60 max=100 value=$config.socialfeed_pinterest_username}
  {control type=text name='socialfeed_pinterest_boardname' label='Pinterest Board Name'|gettext size=60 max=100 value=$config.socialfeed_pinterest_boardname description='Optional entry to get a specific board'|gettext}
  {*{control type=checkbox name="socialfeed_pinterest_post_link" value=1 label="Show Post URL"|gettext checked=$config.socialfeed_pinterest_post_link|default:false}*}
{/group}

{script unique="editchecks" jquery=1}
{literal}
$('#socialfeed_facebook_use').change(function() {
    if ($('#socialfeed_facebook_use').is(':checked') == false)
        $("#facebook").hide("slow");
    else {
        $("#facebook").show("slow");
    }
});
if ($('#socialfeed_facebook_use').is(':checked') == false)
    $("#facebook").hide("slow");

$('#socialfeed_twitter_use').change(function() {
    if ($('#socialfeed_twitter_use').is(':checked') == false)
        $("#twitter").hide("slow");
    else {
        $("#twitter").show("slow");
    }
});
if ($('#socialfeed_twitter_use').is(':checked') == false)
    $("#twitter").hide("slow");

$('#socialfeed_instagram_use').change(function() {
    if ($('#socialfeed_instagram_use').is(':checked') == false)
        $("#instagram").hide("slow");
    else {
        $("#instagram").show("slow");
    }
});
if ($('#socialfeed_instagram_use').is(':checked') == false)
    $("#instagram").hide("slow");

$('#socialfeed_pinterest_use').change(function() {
    if ($('#socialfeed_pinterest_use').is(':checked') == false)
        $("#pinterest").hide("slow");
    else {
        $("#pinterest").show("slow");
    }
});
if ($('#socialfeed_pinterest_use').is(':checked') == false)
    $("#pinterest").hide("slow");
{/literal}
{/script}
