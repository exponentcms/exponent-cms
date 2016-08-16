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

{uniqueid prepend="news" assign="name"}

<div class="module news showall">
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
            |  {icon controller=expTag class="manage" action=manage_module model='news' text="Manage Tags"|gettext}
            {/if}
            {*{if $rank == 1}*}
            {if $config.order == 'rank'}
            |  {ddrerank items=$page->records model="news" label="News Items"|gettext}
            {/if}
        {/if}
        {if $permissions.showUnpublished}
             |  {icon class="view" action=showUnpublished text="View Expired/Unpublished News"|gettext}
        {/if}
    </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {subscribe_link}
    <div id="{$name}list">
        {exp_include file='newslist.tpl'}
    </div>
</div>

{if $smarty.const.AJAX_PAGING}
{if empty($params.page)}
    {$params.page = 1}
{/if}
{script unique="`$name`itemajax" jquery="jquery.history"}
{literal}
    $(document).ready(function() {
        var newslist_{/literal}{$name}{literal} = $('#{/literal}{$name}{literal}list');
        var page_parm_{/literal}{$name}{literal} = '';
        if (EXPONENT.SEF_URLS) {
            page_parm_{/literal}{$name}{literal} = '/page/';
        } else {
            page_parm_{/literal}{$name}{literal} = '&page=';
        }
        var History = window.History;
        History.pushState({name:'{/literal}{$name}{literal}',rel:'{/literal}{$params.page}{literal}'});
        {/literal}
            {$orig_params = ['controller' => 'news', 'action' => 'showall', 'src' => $params.src]}
        {literal}
        var orig_url_{/literal}{$name}{literal} = '{/literal}{makeLink($orig_params)}{literal}';
        var sUrl_{/literal}{$name}{literal} = EXPONENT.PATH_RELATIVE + "index.php?controller=news&action=showall&view=newslist&ajax_action=1&src={/literal}{$__loc->src}{literal}";

        // ajax load new items
        var handleSuccess_{/literal}{$name}{literal} = function(o, ioId){
            if(o){
                newslist_{/literal}{$name}{literal}.html(o);
                newslist_{/literal}{$name}{literal}.find('script').each(function(k, n){
                    if(!$(n).attr('src')){
                        eval($(n).html);
                    } else {
                        $.getScript($(n).attr('src'));
                    };
                });
                newslist_{/literal}{$name}{literal}.find('link').each(function(k, n){
                    $("head").append("  <link href=\"" + $(n).attr('href') + "\" rel=\"stylesheet\" type=\"text/css\" />");
                });
            } else {
                $('#{/literal}{$name}{literal}item.loadingdiv').remove();
                newslist_{/literal}{$name}{literal}.html('Unable to load content');
                newslist_{/literal}{$name}{literal}.css('opacity', 1);
            }
        };

        newslist_{/literal}{$name}{literal}.delegate('a.pager', 'click', function(e){
            e.preventDefault();
            History.pushState({name:'{/literal}{$name}{literal}', rel:$(this)[0].rel}, '{/literal}{'News Items'|gettext}{literal}', orig_url_{/literal}{$name}{literal} + page_parm_{/literal}{$name}{literal} + $(this)[0].rel);
            // moving to a new items
            $.ajax({
                type: "POST",
                headers: { 'X-Transaction': 'Load Items'},
                url: sUrl_{/literal}{$name}{literal},
                data: "page=" + $(this)[0].rel,
                success: handleSuccess_{/literal}{$name}{literal}
            });
            // newslist_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Items"|gettext}{literal}'));
            newslist_{/literal}{$name}{literal}.find('.loader').html($('{/literal}{loading span=1 title="Loading Items"|gettext}{literal}'));
        });

        // Watches the browser history for changes
        window.addEventListener('popstate', function(e) {
            state = History.getState();
            if (state.data.name == '{/literal}{$name}{literal}') {
                // moving to a new items
                $.ajax({
                    type: "POST",
                    headers: { 'X-Transaction': 'Load Items'},
                    url: sUrl_{/literal}{$name}{literal},
                    data: "page=" + state.data.rel,
                    success: handleSuccess_{/literal}{$name}{literal}
                });
                // newslist_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Items"|gettext}{literal}'));
                newslist_{/literal}{$name}{literal}.find('.loader').html($('{/literal}{loading span=1 title="Loading Items"|gettext}{literal}'));
            }
        });
    });
{/literal}
{/script}
{/if}
