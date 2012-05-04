{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

<div id='edituser' class="module users edit">
    {form action=update}
	    <div class="info-header">
	        <div class="related-actions">
                {help text="Get Help"|gettext|cat:" "|cat:("Editing User Accounts"|gettext) module="edit-user"}
	        </div>
			{if $edit_user->id == ""}
				<h1>{'Create a New User Account'|gettext}</h1>
			{else}
				<h1>{'Edit User'|gettext} - '{$edit_user->username}'</h1> ( {'Date of last login'|gettext} {$edit_user->last_login|format_date})
			{/if}
	    </div>
	    <div id="edituser-tabs" class="yui-navset exp-skin-tabview hide">
		    <ul class="yui-nav">
		        <li class="selected"><a href="#tab1"><em>{"General"|gettext}</em></a></li>
                {foreach from=$extensions item=extension}
		            <li><a href="#tab{$extension->id}"><em>{$extension->title}</em></a></li>
                {/foreach}
		    </ul>
	        <div class="yui-content">
	            <div id="tab1">
	                {if $edit_user->id == "" || $edit_user->id == 0}
                        {if $smarty.const.USER_REGISTRATION_USE_EMAIL == 0}
                            {control type=text name=username label="Username"|gettext value=$edit_user->username}
                        {else}
                            {control type=text name=email label="Email Address"|gettext value=$edit_user->email}
                        {/if}
                        {control type=password name=pass1 label="Password"|gettext}
                        {control type=password name=pass2 label="Confirm Password"|gettext}
                    {else}
                        {control type="hidden" name="id" value=$edit_user->id}
	                {/if}
	                {control type="hidden" name="userkey" value=$userkey}
	                {if $smarty.const.USER_REGISTRATION_USE_EMAIL == 0}
                        {control type=text name=email label="Email Address"|gettext value=$edit_user->email}
                    {/if}
	                {control type=text name=firstname label="First Name"|gettext value=$edit_user->firstname}
	                {control type=text name=lastname label="Last Name"|gettext value=$edit_user->lastname}
	                {*control type=checkbox name="recv_html" label="I prefer HTML Email" value=1 checked=$edit_user->recv_html*}
	                {if $user->isAdmin()}
                        {if $user->isSuperAdmin()} {* only super admins can create/change admins *}
                            {control type=checkbox name=is_acting_admin value=1 label="Make this user an Administrator?"|gettext checked=$edit_user->is_acting_admin}
                        {else}
                            {control type=checkbox readonly="readonly" name=is_acting_admin value=1 label="This user is an Administrator?"|gettext checked=$edit_user->is_acting_admin}
                            {if $edit_user->is_acting_admin}{control type=hidden name=is_acting_admin value=1}{/if}
                        {/if}
                        {if $user->isSuperAdmin()}
                            {if $user->id == 1}  {* only the real super admin can create/change other super admins *}
                                {control type=checkbox name=is_admin value=1 label="Make this user a Super Administrator?"|gettext checked=$edit_user->is_admin}
                            {else}
                                {control type=checkbox readonly="readonly" name=is_admin value=1 label="This user is a Super Administrator?"|gettext checked=$edit_user->is_admin}
                                {if $edit_user->is_admin}{control type=hidden name=is_admin value=1}{/if}
                            {/if}
                        {/if}
                    {/if}
	            </div>
	            {foreach from=$extensions item=extension}
	            <div id="tab{$extension->id}" >
	                {include file="`$smarty.const.BASE`framework/modules/users/views/extensions/`$extension->classname`.tpl"}
	            </div>
	            {/foreach}
	        </div>
	    </div>
	    <div class="loadingdiv">{'Loading User Profile'|gettext}</div>
	    {if $user->isAdmin() == 0}
			{control type=antispam}
		{/if}
	    {control type="buttongroup" submit="Submit"|gettext cancel="Cancel"|gettext}
	{/form}
</div>

{script unique="edituser" yui3mods=1}
{literal}
	YUI(EXPONENT.YUI3_CONFIG).use('tabview', function(Y) {
		var tabview = new Y.TabView({srcNode:'#edituser-tabs'});
		tabview.render();
		Y.one('#edituser-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
	});
{/literal}
{/script}
