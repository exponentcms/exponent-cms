{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
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

{if !$error}
{uniqueid prepend="sermon" assign="name"}

{css unique="sermon" link="`$asset_path`css/sermons.css" corecss="common"}

{/css}
{css unique="mediaelement" link="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelementplayer.min.css"}

{/css}

<div class="module sermonseries show">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{/if}
    {rss_link}{if $config.show_comments}{rss_link type=comments title='Subscribe to Sermon Comments'|gettext}{/if}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {icon class=view action=showall text="Show All Sermon Series"|gettext}
    {permissions}
        {if $permissions.manage}
            {icon class=import action=import_filedownloads text="Import File Download Items"|gettext}
            {if !$config.disabletags}
                {icon controller=expTag class="manage" action=manage_module model='sermonseries' text="Manage Tags"|gettext}
            {/if}
        {/if}
    {/permissions}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    <div class="row">
        <div class="col-sm-8">
            {subscribe_link}
            <{$config.item_level|default:'h2'}>{'Series Title'|gettext}: {$record->title}</{$config.item_level|default:'h2'}>
            {permissions}
                <div class="item-actions">
                    {if $permissions.edit || ($permissions.create && $record->poster == $user->id)}
                        {icon action=edit record=$record text="Edit Series"|gettext title="Edit this Sermon Series"|gettext}
                    {/if}
                    {if $permissions.delete || ($permissions.create && $record->poster == $user->id)}
                        {icon action=delete record=$record text="Delete Series"|gettext title="Delete this Sermon Series and its Sermons"|gettext onclick="return confirm('Are you sure you want to delete this entire Sermon Series?');"}
                    {/if}
                </div>
            {/permissions}
            {$record->body}
        </div>
        <div class="col-sm-4 hidden-xs">
            {if $record->expFile.preview[0] != ""}
                {img class="preview-img img-responsive" file_id=$record->expFile.preview[0]->id h=105 w=155 alt=$record->title}
            {else}
                {img class="preview-img img-responsive" src="`$asset_path`images/sermon.jpeg" h=105 w=155 alt=$record->title}
            {/if}
        </div>
    </div>
    {permissions}
        <div class="module-actions">
            {if $permissions.create}
                {icon class=add action=edit_sermon rank=1 record=$record title="Add a Sermon to this series"|gettext text="Add a Sermon to this series"|gettext}
            {/if}
            {if ($permissions.manage && $config.order == 'rank')}
                {ddrerank items=$record->sermons model="sermons" label="Sermons"|gettext}
            {/if}
        </div>
    {/permissions}
    {$hide_attachments = true}
    <div id="{$name}item">
        {if count($record->sermons)}
            {exp_include file='sermon.tpl'}
        {else}
            <h4>{'There are no sermons in this series'|gettext}</h4>
        {/if}
    </div>
</div>

{script unique="mediaelement-src" jquery="1" src="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelement-and-player.min.js"}
{/script}

{if $smarty.const.AJAX_PAGING}
{script unique="`$name`itemajax" jquery="jquery.history"}
{literal}
    $(document).ready(function() {
        var sermon_{/literal}{$name}{literal} = $('#{/literal}{$name}{literal}item');
        var page_parm_{/literal}{$name}{literal} = '';
        if (EXPONENT.SEF_URLS) {
            // page_parm_{/literal}{$name}{literal} = '/page/';
            page_parm_{/literal}{$name}{literal} = '/';  // compensate for router_maps.php
        } else {
            page_parm_{/literal}{$name}{literal} = '&page=';
        }
        var History = window.History;
        History.pushState({name:'{/literal}{$name}{literal}',rel:'{/literal}{$params.title}{literal}'});
        {/literal}
            {$orig_params = ['controller' => 'sermonseries', 'action' => 'show', 'title' => $params.title]}
        {literal}
        var orig_url_{/literal}{$name}{literal} = '{/literal}{makeLink($orig_params)}{literal}';
        var sUrl_{/literal}{$name}{literal} = EXPONENT.PATH_RELATIVE + "index.php?controller=sermonseries&action=show&view=sermon&ajax_action=1&src={/literal}{$__loc->src}{literal}&title={/literal}{$record->sef_url}{literal}";

        // ajax load new sermon
        var handleSuccess_{/literal}{$name}{literal} = function(o, ioId){
            if(o){
                sermon_{/literal}{$name}{literal}.html(o);
                sermon_{/literal}{$name}{literal}.find('script').each(function(k, n){
                    if(!$(n).attr('src')){
                        eval($(n).html);
                    } else {
                        $.getScript($(n).attr('src'));
                    };
                });
                sermon_{/literal}{$name}{literal}.find('link').each(function(k, n){
                    $("head").append("  <link href=\"" + $(n).attr('href') + "\" rel=\"stylesheet\" type=\"text/css\" />");
                });
            } else {
                $('#{/literal}{$name}{literal}item.loadingdiv').remove();
                sermon_{/literal}{$name}{literal}.html('Unable to load content');
                sermon_{/literal}{$name}{literal}.css('opacity', 1);
            }
        };

        sermon_{/literal}{$name}{literal}.delegate('a.s-pager', 'click', function(e){
            e.preventDefault();
            History.pushState({name:'{/literal}{$name}{literal}', rel:$(this)[0].rel}, '{/literal}{'Sermons'|gettext}{literal}', orig_url_{/literal}{$name}{literal} + page_parm_{/literal}{$name}{literal} + $(this)[0].rel);
            // moving to a new sermon
            $.ajax({
                type: "POST",
                headers: { 'X-Transaction': 'Load Sermon'},
                url: sUrl_{/literal}{$name}{literal},
                data: "page=" + $(this)[0].rel,
                success: handleSuccess_{/literal}{$name}{literal}
            });
//            sermon_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Sermon"|gettext}{literal}'));
            sermon_{/literal}{$name}{literal}.find('.loader').html($('{/literal}{loading span=1 title="Loading Sermon"|gettext}{literal}'));
        });

        // Watches the browser history for changes
        window.addEventListener('popstate', function(e) {
            state = History.getState();
            if (state.data.name == '{/literal}{$name}{literal}') {
                // moving to a new sermon
                $.ajax({
                    type: "POST",
                    headers: { 'X-Transaction': 'Load Sermon'},
                    url: sUrl_{/literal}{$name}{literal},
                    data: "page=" + state.data.rel,
                    success: handleSuccess_{/literal}{$name}{literal}
                });
//                sermon_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Sermon"|gettext}{literal}'));
                sermon_{/literal}{$name}{literal}.find('.loader').html($('{/literal}{loading span=1 title="Loading Sermon"|gettext}{literal}'));
            }
        });
    });
{/literal}
{/script}
{/if}
{/if}
