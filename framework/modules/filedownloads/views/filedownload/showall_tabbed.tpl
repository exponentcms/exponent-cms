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

{if $config.usecategories}
{css unique="categories" corecss="categories"}

{/css}
{/if}
{css unique="mediaelement" link="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelementplayer.css"}

{/css}

<div class="module filedownload showall showall-tabbed">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{/if}
    {rss_link}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}</h1>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit rank=1 title="Add a File at the Top"|gettext text="Add a File"|gettext}
			{/if}
            {if $permissions.manage == 1}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='filedownload' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='filedownload' text="Manage Categories"|gettext}
                {/if}
                {*{if $rank == 1}*}
                {if $config.order == 'rank'}
                    {ddrerank items=$page->records model="filedownload" label="Downloadable Items"|gettext}
                {/if}
           {/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {subscribe_link}
    {$myloc=serialize($__loc)}
    <div id="{$id}" class="yui-navset exp-skin-tabview">
        <ul class="yui-nav">
            {foreach name=tabs from=$page->cats key=catid item=cat}
                <li><a href="#tab{$smarty.foreach.tabs.iteration}">{$cat->name}</a></li>
            {/foreach}
        </ul>
        <div class="yui-content">
            {foreach name=items from=$page->cats key=catid item=cat}
                <div id="tab{$smarty.foreach.items.iteration}">
                    {foreach from=$cat->records item=file}
                        {include 'filedownloaditem.tpl'}
                    {/foreach}
                </div>
            {/foreach}
        </div>
    </div>
    <div class="loadingdiv">{'Loading'|gettext}</div>
</div>

{*{script unique="`$id`" yui3mods="1"}*}
{*{literal}*}
    {*EXPONENT.YUI3_CONFIG.modules.exptabs = {*}
        {*fullpath: EXPONENT.JS_RELATIVE+'exp-tabs.js',*}
        {*requires: ['history','tabview','event-custom']*}
    {*};*}

	{*YUI(EXPONENT.YUI3_CONFIG).use('exptabs', function(Y) {*}
        {*Y.expTabs({srcNode: '#{/literal}{$id}{literal}'});*}
		{*Y.one('#{/literal}{$id}{literal}').removeClass('hide');*}
		{*Y.one('.loadingdiv').remove();*}
	{*});*}
{*{/literal}*}
{*{/script}*}

{script unique="`$id`" jquery="jqueryui"}
{literal}
    $('#{/literal}{$id}{literal}').tabs().next().remove();
{/literal}
{/script}

{if $config.show_player}
    {script unique="mediaelement-src" jquery="1" src="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelement-and-player.min.js"}
    {/script}

    {script unique="filedownload-`$id`"}
        $('audio,video').mediaelementplayer({
        	success: function(player, node) {
        		$('#' + node.id + '-mode').html('mode: ' + player.pluginType);
        	}
        });
    {/script}
{/if}
