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

{css unique="edit_user" corecss="tables"}

{/css}

<div id='edituser' class="module users edit">
    {form action=update}
	    <div class="info-header">
	        <div class="related-actions">
                {help text="Get Help with"|gettext|cat:" "|cat:("Editing User Accounts"|gettext) module="edit-user"}
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
                {if !empty($groups->records)}
                    <li class="selected"><a href="#tab2"><em>{"Group Membership"|gettext}</em></a></li>
                {/if}
                {foreach from=$extensions item=extension}
		            <li><a href="#tab{$extension->id+2}"><em>{$extension->title}</em></a></li>
                {/foreach}
		    </ul>
	        <div class="yui-content">
	            <div id="tab1">
	                {*{if $edit_user->id == "" || $edit_user->id == 0}*}
                    {if empty($edit_user->id)}
                        {if $smarty.const.USER_REGISTRATION_USE_EMAIL == 0}
                            {control type=text name=username label="Username"|gettext value=$edit_user->username required=1}
                        {else}
                            {*{control type=text name=email label="Email Address"|gettext value=$edit_user->email required=1}*}
                            {control type=email name=email label="Email Address"|gettext value=$edit_user->email required=1}
                        {/if}
                        {control type=password name=pass1 label="Password"|gettext required=1}
                        {control type=password name=pass2 label="Confirm Password"|gettext required=1}
                    {else}
                        {control type="hidden" name="id" value=$edit_user->id}
	                {/if}
                    {control type="hidden" name="userkey" value=$userkey}
	                {if $smarty.const.USER_REGISTRATION_USE_EMAIL == 0}
                        {*{control type=text name=email label="Email Address"|gettext value=$edit_user->email}*}
                        {control type=email name=email label="Email Address"|gettext value=$edit_user->email}
                    {/if}
	                {control type=text name=firstname label="First Name"|gettext value=$edit_user->firstname}
	                {control type=text name=lastname label="Last Name"|gettext value=$edit_user->lastname}
	                {*control type=checkbox name="recv_html" label="I prefer HTML Email" value=1 checked=$edit_user->recv_html*}
	                {if $user->isAdmin()}
                        {if $smarty.const.USE_LDAP}
                            {control type=checkbox name=is_ldap value=1 label="Use LDAP Authentication?"|gettext checked=$edit_user->is_ldap}
                        {/if}
                        {if $user->isSuperAdmin()} {* only super admins can create/change admins *}
                            {control type=checkbox name=is_acting_admin value=1 label="Make this user an Administrator?"|gettext checked=$edit_user->is_acting_admin}
                        {else}
                            {control type=checkbox readonly="readonly" name=is_acting_admin value=1 label="This user is an Administrator?"|gettext checked=$edit_user->is_acting_admin}
                            {if $edit_user->is_acting_admin}{control type=hidden name=is_acting_admin value=1}{/if}
                        {/if}
                        {if $user->isSuperAdmin()}
                            {if $user->is_system_user}  {* only the real super admin can create/change other super admins *}
                                {control type=checkbox name=is_admin value=1 label="Make this user a Super Administrator?"|gettext checked=$edit_user->is_admin}
                            {else}
                                {control type=checkbox readonly="readonly" name=is_admin value=1 label="This user is a Super Administrator?"|gettext checked=$edit_user->is_admin}
                                {if $edit_user->is_admin}{control type=hidden name=is_admin value=1}{/if}
                            {/if}
                        {/if}
                    {/if}
	            </div>
                {if !empty($groups->records)}
                <div id="tab2">
                    {pagelinks paginate=$groups top=1}
                	<table class="exp-skin-table">
                	    <thead>
                			<tr>
                				{$groups->header_columns}
                                <th>{'Member'|gettext}</th>
                			</tr>
                		</thead>
                		<tbody>
                			{foreach from=$groups->records item=group name=listings}
                                <tr class="{cycle values="odd,even"}">
                                    <td>{$group->name}</td>
                                    <td>{$group->description}</td>
                                    <td>
                                        {$checked = false}
                                        {foreach from=$mygroups item=mygroup}
                                            {if $mygroup->id == $group->id}
                                                {$checked = true}
                                            {/if}
                                        {/foreach}
                                        {if $edit_user->isAdmin()}
                                            {control type=checkbox name='member[]' value=$group->id checked=$checked disabled=true}
                                        {else}
                                            {control type=checkbox name='member[]' value=$group->id checked=$checked}
                                        {/if}
                                    </td>
                                </tr>
                			{foreachelse}
                			    <tr><td colspan="{$groups->columns|count}">{'No User Groups Available'|gettext}.</td></tr>
                			{/foreach}
                		</tbody>
                	</table>
                    {pagelinks paginate=$groups bottom=1}
                </div>
                {/if}
	            {foreach from=$extensions item=extension}
                    <div id="tab{$extension->id+2}" >
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
    EXPONENT.YUI3_CONFIG.modules.exptabs = {
        fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',
        requires: ['history','tabview','event-custom']
    };

	YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {
        Y.expTabs({srcNode: '#edituser-tabs'});
		Y.one('#edituser-tabs').removeClass('hide');
		Y.one('.loadingdiv').remove();
	});
{/literal}
{/script}
