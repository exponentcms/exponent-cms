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

{uniqueid prepend="sermon" assign="name"}

{css unique="sermon" link="`$asset_path`css/sermons.css" corecss="common"}

{/css}

<div class="module sermonseries showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{/if}
    {rss_link}{if $config.show_comments}{rss_link type=comments title='Subscribe to Sermon Comments'|gettext}{/if}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {subscribe_link}
    {permissions}
        <div class="module-actions">
            {if $permissions.create}
                {icon class=add action=edit rank=1 title="Add a Sermon Series"|gettext text="Add a Sermon Series"|gettext}
            {/if}
            {if $permissions.manage}
                {ddrerank items=$page->records model="sermonseries" label="Sermon Series"|gettext}
                {if $config.show_speaker}
                    {icon class=manage action=manage_speakers text="Manage Speakers"|gettext}
                {/if}
                {if $config.show_service}
                    {icon class=manage action=manage_services text="Manage Services"|gettext}
                {/if}
                {if !empty($page->records)}
                    {icon class=import action=import_filedownloads text="Import File Download Items"|gettext}
                    {*{icon class=import action=import_blogs text="Import Blog Items"|gettext}*}
                    {if $rank == 1}
                        {ddrerank items=$page->records model="sermonseries" label="Sermon Series"|gettext}
                    {/if}
                {/if}
            {/if}
        </div>
    {/permissions}
    <div id="{$name}list">
        {exp_include file='sermonlist.tpl'}
    </div>
</div>

{if $smarty.const.AJAX_PAGING}
{if empty($params.page)}
    {$params.page = 1}
{/if}
{script unique="`$name`itemajax" jquery="jquery.history"}
{literal}
    $(document).ready(function() {
        var sermonlist_{/literal}{$name}{literal} = $('#{/literal}{$name}{literal}list');
        var page_parm_{/literal}{$name}{literal} = '';
        if (EXPONENT.SEF_URLS) {
            page_parm_{/literal}{$name}{literal} = '/page/';
        } else {
            page_parm_{/literal}{$name}{literal} = '&page=';
        }
        var History = window.History;
        History.pushState({name:'{/literal}{$name}{literal}',rel:'{/literal}{$params.page}{literal}'});
        {/literal}
            {$orig_params = ['controller' => 'sermonseries', 'action' => 'showall', 'src' => $__loc->src]}
        {literal}
        var orig_url_{/literal}{$name}{literal} = '{/literal}{makeLink($orig_params)}{literal}';
        var sUrl_{/literal}{$name}{literal} = EXPONENT.PATH_RELATIVE + "index.php?controller=sermonseries&action=showall&view=sermonlist&ajax_action=1&src={/literal}{$__loc->src}{literal}";

        // ajax load new sermon series
        var handleSuccess_{/literal}{$name}{literal} = function(o, ioId){
            if(o){
                sermonlist_{/literal}{$name}{literal}.html(o);
                sermonlist_{/literal}{$name}{literal}.find('script').each(function(k, n){
                    if(!$(n).attr('src')){
                        eval($(n).html);
                    } else {
                        $.getScript($(n).attr('src'));
                    };
                });
                sermonlist_{/literal}{$name}{literal}.find('link').each(function(k, n){
                    $("head").append("  <link href=\"" + $(n).attr('href') + "\" rel=\"stylesheet\" type=\"text/css\" />");
                });
            } else {
                $('#{/literal}{$name}{literal}item.loadingdiv').remove();
                sermonlist_{/literal}{$name}{literal}.html('Unable to load content');
                sermonlist_{/literal}{$name}{literal}.css('opacity', 1);
            }
        };

        sermonlist_{/literal}{$name}{literal}.delegate('a.pager', 'click', function(e){
            e.preventDefault();
            History.pushState({name:'{/literal}{$name}{literal}', rel:$(this)[0].rel}, '{/literal}{'Sermon Series'|gettext}{literal}', orig_url_{/literal}{$name}{literal} + page_parm_{/literal}{$name}{literal} + $(this)[0].rel);
            // moving to a new sermon series
            $.ajax({
                type: "POST",
                headers: { 'X-Transaction': 'Load Sermon Series'},
                url: sUrl_{/literal}{$name}{literal},
                data: "page=" + $(this)[0].rel,
                success: handleSuccess_{/literal}{$name}{literal}
            });
            // sermonlist_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Sermon Series"|gettext}{literal}'));
            sermonlist_{/literal}{$name}{literal}.find('.loader').html($('{/literal}{loading span=1 title="Loading Sermon Series"|gettext}{literal}'));
        });

        // Watches the browser history for changes
        window.addEventListener('popstate', function(e) {
            state = History.getState();
            if (state.data.name == '{/literal}{$name}{literal}') {
                // moving to a new sermon series
                $.ajax({
                    type: "POST",
                    headers: { 'X-Transaction': 'Load Sermon Series'},
                    url: sUrl_{/literal}{$name}{literal},
                    data: "page=" + state.data.rel,
                    success: handleSuccess_{/literal}{$name}{literal}
                });
                // sermonlist_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Sermon Series"|gettext}{literal}'));
                sermonlist_{/literal}{$name}{literal}.find('.loader').html($('{/literal}{loading span=1 title="Loading Sermon Series"|gettext}{literal}'));
            }
        });
    });
{/literal}
{/script}
{/if}
