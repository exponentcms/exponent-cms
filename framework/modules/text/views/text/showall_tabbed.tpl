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
 
{uniqueid assign="id"}

<div class="module text showall-tabbed">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
        <div class="module-actions">
            {*{if $permissions.create}*}
                {*{icon class=add action=edit rank=1 text="Add Text Tab"|gettext}*}
            {*{/if}*}
            {if $permissions.manage}
                {ddrerank items=$items model="text" label="Text Items"|gettext}
            {/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {$myloc=serialize($__loc)}
    {*{if !count($items)}*}
        {*{permissions}*}
            {*<div class="msg-queue notice" style="text-align:center"><p>{'There are no text items/tabs in the module!'|gettext}</p></div>*}
        {*{/permissions}*}
    {*{/if}*}
    {*{else}*}
    <div id="text-{$id}" class="yui-navset exp-skin-tabview">
        <ul class="yui-nav">
            {foreach from=$items item=tab name=tabs}
                <li><a href="#tab{$smarty.foreach.tabs.iteration}">{if $tab->title ==""}&#160;{else}{$tab->title}{/if}</a></li>
            {/foreach}
            {permissions}
                {if ($permissions.create)}
                    {if $smarty.foreach.tabs.iteration != 0}
                        <li>
                    {else}
                        <li class="selected">
                    {/if}
                    <a href="#tab{$smarty.foreach.tabs.iteration+1}"><em>({'Add New'|gettext})</em></a></li>
                {/if}
            {/permissions}
        </ul>
        <div class="yui-content">
            {foreach from=$items item=text name=items}
                <div id="tab{$smarty.foreach.items.iteration}" class="item">
                    {permissions}
						<div class="item-actions">
						   {if $permissions.edit || ($permissions.create && $text->poster == $user->id)}
                                {if $myloc != $text->location_data}
                                    {if $permissions.manage}
                                        {icon action=merge id=$text->id title="Merge Aggregated Content"|gettext}
                                    {else}
                                        {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                    {/if}
                                {/if}
								{icon action=edit record=$text}
							{/if}
							{if $permissions.delete || ($permissions.create && $text->poster == $user->id)}
								{icon action=delete record=$text}
							{/if}
						</div>
                    {/permissions}
                    <div class="bodycopy">
                        {if $config.ffloat != "Below"}
                            {filedisplayer view="`$config.filedisplay`" files=$text->expFile record=$text}
                        {/if}
                        {$text->body}
                        {if $config.ffloat == "Below"}
                            {filedisplayer view="`$config.filedisplay`" files=$text->expFile record=$text}
                        {/if}
                    </div>
                    {clear}
                </div>
            {/foreach}
            {permissions}
                {if $permissions.create}
                    <div id="tab{$smarty.foreach.tabs.iteration+1}">
                        {icon class=add action=edit rank=$text->rank+1 text="Add more text here"|gettext}
                    </div>
                {/if}
            {/permissions}
        </div>
    </div>
    <div class="loadingdiv">{'Loading'|gettext}</div>
</div>

{*{script unique="text-`$id`" yui3mods="1"}*}
{*{literal}*}
    {*EXPONENT.YUI3_CONFIG.modules.exptabs = {*}
        {*fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',*}
        {*requires: ['history','tabview','event-custom']*}
    {*};*}

	{*YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {*}
        {*Y.expTabs({srcNode: '#text-{/literal}{$id}{literal}'});*}
		{*Y.one('#text-{/literal}{$id}{literal}').removeClass('hide');*}
		{*Y.one('.loadingdiv').remove();*}
	{*});*}
{*{/literal}*}
{*{/script}*}

    {script unique="text-`$id`" jquery="jqueryui"}
    {literal}
        $('#text-{/literal}{$id}{literal}').tabs().next().remove();
    {/literal}
    {/script}
{*{/if}*}
