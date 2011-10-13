{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by James Hunt
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

<div id='userconfig' class="module users edit exp-skin-tabview">
    {script unique="userconfig" yui3mods=1}
    {literal}
    YUI(EXPONENT.YUI3_CONFIG).use('node','yui2-tabview','yui2-element', function(Y) {
	    var YAHOO=Y.YUI2;
        var tabView = new YAHOO.widget.TabView('demo');
        Y.one('#userconfig').removeClass('hide');
        Y.one('.loadingdiv').remove();
    });
    {/literal}
    {/script}


    <div class="info-header">
        <div class="related-actions">
                {help text="Get Help with User Accounts" module="edit-user"}
        </div>
		{if $edit_user->id == ""}
			<h1>Create a New User Account</h1>
		{else}
			<span><h1>Edit User - '{$edit_user->username}'</h1> ( Date of last login {$edit_user->last_login|format_date})</span>
		{/if}    
        </div>
    <div id="demo" class="yui-navset">
    <ul class="yui-nav">
        <li class="selected"><a href="#tab1"><em>{gettext str="General"}</em></a></li>

            {foreach from=$extensions item=extension}
        <li><a href="#tab{$extension->id}"><em>{$extension->title}</em></a></li>
            {/foreach}
    </ul>
        <div class="yui-content">
            {form action=update}
            <div id="tab1">
                {if $edit_user->id == "" || $edit_user->id == 0}
                        {if $smarty.const.USER_REGISTRATION_USE_EMAIL == 0}
                                {control type=text name=username label="Username" value=$edit_user->username}
                        {else}
                                {control type=text name=email label="Email Address" value=$edit_user->email}
                        {/if}
                        {control type=password name=pass1 label=Password}
                        {control type=password name=pass2 label="Confirm Password"}
                    {else}
                        {control type="hidden" name="id" value=$edit_user->id}
                {/if}
                {control type="hidden" name="userkey" value=$userkey}
                {if $smarty.const.USER_REGISTRATION_USE_EMAIL == 0}{control type=text name=email label="Email Address" value=$edit_user->email}{/if}
                {control type=text name=firstname label="First Name" value=$edit_user->firstname}
                {control type=text name=lastname label="Last Name" value=$edit_user->lastname}
                {*control type=checkbox name="recv_html" label="I prefer HTML Email" value=1 checked=$edit_user->recv_html*}
                {if $user->isAdmin() == 1}
                    {if $edit_user->id==$user->id || $user->isActingAdmin()}
                            {control type=checkbox readonly="readonly" name=is_acting_admin value=1 label="Make this user an Administrator?" checked=$edit_user->is_acting_admin}
                    {else}
                        {control type=checkbox name=is_acting_admin value=1 label="Make this user an Administrator?." checked=$edit_user->is_acting_admin}
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

    {if $user->isAdmin() == 0}
		{control type=antispam}
	{/if}
    {control type="buttongroup" submit="Submit" cancel="Cancel"}
{/form}

</div>
<div class="loadingdiv">Loading</div>
