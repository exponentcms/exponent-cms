{*
 * Copyright (c) 2004-2021 OIC Group, Inc.
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

{css unique="accordion" corecss="accordion"}

{/css}
{css unique="mediaelement" link="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelementplayer.min.css"}

{/css}

{uniqueid assign="id"}

<div class="module filedownload showall showall-accordion">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{/if}
    {rss_link}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit rank=1 title="Add a File at the Top"|gettext text="Add a File"|gettext}
			{/if}
            {if $permissions.manage}
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
    <div id="file-{$id}" class="accordion">
        {foreach name=items from=$page->cats key=catid item=cat}
            <div id="item{$catid}" class="accordion-item">
                <{$config.item_level|default:'h2'} class="accordion-header" id="heading-{$catid}">
                    <button class="accordion-button{if !(($smarty.foreach.items.iteration==1 && $config.initial_view == '3') || $config.initial_view == '2')} collapsed{/if}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{$catid}" aria-expanded="true" aria-controls="collapse-{$catid}">{if $item->title ==""}&#160;{else}{$item->title}{/if}</button>
                </{$config.item_level|default:'h2'}>
                <div id="collapse-{$catid}" class="accordion-collapse collapse{if ($smarty.foreach.items.iteration==1 && $config.initial_view == '3') || $config.initial_view == '2'} show{/if}" aria-labelledby="heading-{$catid}" data-bs-parent="#file-{$id}">
                    <div class="piece accordion-body">
                        <ul>
                            {foreach from=$cat->records item=item}
                                <li>
                                    {exp_include file='filedownloaditem.tpl'}
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                </div>
            </div>
        {/foreach}
    </div>
</div>

{script unique="accordion" bootstrap="collapse"}
{literal}

{/literal}
{/script}

{if $config.show_player}
    {script unique="mediaelement-src" jquery="1" src="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelement-and-player.min.js"}

    {/script}

    {script unique="filedownload-`$id`"}
    {literal}
        mejs.i18n.language('{/literal}{substr($smarty.const.LOCALE,0,2)}{literal}'); // Setting language
        $('audio,video').mediaelementplayer({
            // Do not forget to put a final slash (/)
            pluginPath: '../build/',
            // this will allow the CDN to use Flash without restrictions
            // (by default, this is set as `sameDomain`)
            shimScriptAccess: 'always',
            success: function(player, node) {
            // $('#' + node.id + '-mode').html('mode: ' + player.rendererName);
            },
        });
    {/literal}
    {/script}
{/if}