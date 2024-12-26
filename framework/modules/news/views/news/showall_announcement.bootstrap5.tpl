{*
 * Copyright (c) 2004-2025 OIC Group, Inc.
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

{css unique="announcement" link="`$asset_path`css/announcement.css"}
{literal}
    .close-icon {
      cursor: pointer;
    }
{/literal}
{/css}

{if !empty($config.enable_facebook_like) || !empty($config.displayfbcomments)}
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v14.0&appId={$config.app_id}&autoLogAppEvents=1" nonce="9wKafjYh"></script>
{/if}

{if $config.enable_tweet}
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
{/if}

<div class="module news announcement">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{/if}
    {rss_link}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}

    {permissions}
    <div class="module-actions">
        {if $permissions.create}
            {icon class="add" action=edit rank=1 text="Add a news post"|gettext}
        {/if}
        {if $permissions.manage}
            {if !$config.disabletags}
                {icon controller=expTag class="manage" action=manage_module model='news' text="Manage Tags"|gettext}
            {/if}
            {*{if $rank == 1}*}
            {if $config.order == 'rank'}
                {ddrerank items=$page->records model="news" label="News Items"|gettext}
            {/if}
            {icon class=view action=scriptaction text='Reveal Hidden Alerts'|gettext title='Reveal Hidden Alerts'|gettext onclick="restoreNodes();"}
        {/if}
        {if $permissions.showUnpublished}
            {icon class="view" action=showUnpublished text="View Expired/Unpublished News"|gettext}
        {/if}
    </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {$myloc=serialize($__loc)}
    {foreach from=$page->records item=item}
        {*<div class="item announcement{if !$item->approved && $smarty.const.ENABLE_WORKFLOW} unapproved{/if}{if $item->is_featured} featured{/if}">*}
        <div id="alert-{$item->sef_url}" class="item card{if !$item->approved && $smarty.const.ENABLE_WORKFLOW} unapproved{/if}{if newsController::getVar('alert-'|cat:$item->sef_url)} d-none{/if}">
            <div class="card-header bg-{if $item->is_featured}danger{else}{cycle values="info,success"}{/if}">
                {if $config.hidefeatured && $item->is_featured}
                    <span class="float-end clickable close-icon" data-effect="fadeOut"><i class="{if $smarty.const.USE_BOOTSTRAP_ICONS}bi-times{else}fas fa-times{/if}"></i></span>
                {/if}
                <{$config.item_level|default:'h2'} class="card-title text-white">{$item->title}</{$config.item_level|default:'h2'}>
            </div>
            <div class="card-body">
            {if $item->isRss != true}
                {permissions}
                    <div class="item-actions">
                        {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                            {if $item->revision_id > 1 && $smarty.const.ENABLE_WORKFLOW}<span class="revisionnum approval" title="{'Viewing Revision #'|gettext}{$item->revision_id}">{$item->revision_id}</span>{/if}
                            {if $myloc != $item->location_data}
                                {if $permissions.manage}
                                    {icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                {else}
                                    {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                {/if}
                            {/if}
                            {icon action=edit record=$item}
                            {icon action=copy record=$item}
                        {/if}
                        {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                            {icon action=delete record=$item}
                        {/if}
                        {if !$item->approved && $smarty.const.ENABLE_WORKFLOW && $permissions.approve && ($permissions.edit || ($permissions.create && $item->poster == $user->id))}
                            {icon action=approve record=$item}
                        {/if}
                    </div>
                {/permissions}
            {/if}
            <div class="bodycopy">
                {if $config.ffloat != "Below"}
                    {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item is_listing=1}
                {/if}
                {$link = '<a href="'|cat:makeLink([controller=>news, action=>show, title=>$item->sef_url])|cat:'"><em>'|cat:'(read more)'|gettext|cat:'</em></a>'}
                {if $config.usebody==1}
                    {*<p>{$item->body|summarize:"html":"paralinks"}</p>*}
                {elseif $config.usebody==3}
                    {$item->body|summarize:"html":"parapaged":$link}
                {elseif $config.usebody==2}
                    <p>{$item->body|summarize:"html":"parahtml":$link}</p>
				{else}
                    {$item->body}
                {/if}
                {if $config.ffloat == "Below"}
                    {filedisplayer view="`$config.filedisplay`" files=$item->expFile record=$item is_listing=1}
                {/if}
            </div>
            {if $config.enable_facebook_like}
                <div class="fb-like" data-href="{link action=show title=$item->sef_url}" data-width="{$config.fblwidth}" data-layout="{$config.fblayout|default:'standard'}" data-action="{$config.fbverb|default:'like'}" data-size="{$config.fblsize|default:'small'}" data-share="true"></div>
            {/if}
            {if $config.enable_tweet}
                <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-text="{$item->title}" data-url="{link action=show title=$item->sef_url}"{if $config.twsize} data-size="{$config.twsize}"{/if} data-show-count="false">{'Tweet'|gettext}</a>
            {/if}
            {clear}
            </div>
        </div>
    {/foreach}
</div>

{if $config.hidefeatured}
    {script unique='hide_featured'}
    {literal}
        $('.close-icon').on('click',function() {
            $(this).closest('.card').addClass('d-none');
            $.post(eXp.PATH_RELATIVE+"index.php?ajax_action=1&module=news&action=setVar", { var:$(this).closest('.card').attr('id'), val:1 });
        })

        function restoreNodes() {
            $( '.item.card.d-none' ).each(function(  ) {
                $( this ).removeClass( "d-none" );
                $.post(eXp.PATH_RELATIVE+"index.php?ajax_action=1&module=news&action=setVar", { var:$(this).attr('id'), val:0 });
            });
        }
    {/literal}
    {/script}
{/if}