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

{uniqueid prepend="blog" assign="name"}

{css unique="blog" link="`$asset_path`css/blog.css"}

{/css}
{if $config.usecategories}
{css unique="categories" corecss="categories"}

{/css}
{/if}

<div class="module blog showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{/if}
    {rss_link}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit text="Add a new blog article"|gettext}
			{/if}
            {if $permissions.manage}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='blog' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='blog' text="Manage Categories"|gettext}
                {/if}
            {/if}
		</div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {subscribe_link}
    <div id="{$name}list">
        {exp_include file='bloglist.tpl'}
    </div>
</div>

{if $smarty.const.AJAX_PAGING}
{if empty($params.page)}
    {$params.page = 1}
{/if}
{script unique="`$name`itemajax" jquery="jquery.history"}
{literal}
    $(document).ready(function() {
        var bloglist_{/literal}{$name}{literal} = $('#{/literal}{$name}{literal}list');
        var page_parm_{/literal}{$name}{literal} = '';
        if (EXPONENT.SEF_URLS) {
            page_parm_{/literal}{$name}{literal} = '/page/';
        } else {
            page_parm_{/literal}{$name}{literal} = '&page=';
        }
        var History = window.History;
        History.pushState({name:'{/literal}{$name}{literal}',rel:'{/literal}{$params.page}{literal}'});
        {/literal}
            {$orig_params = ['controller' => 'blog', 'action' => 'showall', 'src' => $params.src]}
        {literal}
        var orig_url_{/literal}{$name}{literal} = '{/literal}{makeLink($orig_params)}{literal}';
        var sUrl_{/literal}{$name}{literal} = EXPONENT.PATH_RELATIVE + "index.php?controller=blog&action=showall&view=bloglist&ajax_action=1&src={/literal}{$__loc->src}{literal}";

        // ajax load new posts
        var handleSuccess_{/literal}{$name}{literal} = function(o, ioId){
            if(o){
                bloglist_{/literal}{$name}{literal}.html(o);
                bloglist_{/literal}{$name}{literal}.find('script').each(function(k, n){
                    if(!$(n).attr('src')){
                        eval($(n).html);
                    } else {
                        $.getScript($(n).attr('src'));
                    };
                });
                bloglist_{/literal}{$name}{literal}.find('link').each(function(k, n){
                    $("head").append("  <link href=\"" + $(n).attr('href') + "\" rel=\"stylesheet\" type=\"text/css\" />");
                });
            } else {
                $('#{/literal}{$name}{literal}item.loadingdiv').remove();
                bloglist_{/literal}{$name}{literal}.html('Unable to load content');
                bloglist_{/literal}{$name}{literal}.css('opacity', 1);
            }
        };

        bloglist_{/literal}{$name}{literal}.delegate('a.pager', 'click', function(e){
            e.preventDefault();
            History.pushState({name:'{/literal}{$name}{literal}', rel:$(this)[0].rel}, '{/literal}{'Blog Posts'|gettext}{literal}', orig_url_{/literal}{$name}{literal} + page_parm_{/literal}{$name}{literal} + $(this)[0].rel);
            // moving to a new posts
            $.ajax({
                type: "POST",
                headers: { 'X-Transaction': 'Load Posts'},
                url: sUrl_{/literal}{$name}{literal},
                data: "page=" + $(this)[0].rel,
                success: handleSuccess_{/literal}{$name}{literal}
            });
            // bloglist_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Posts"|gettext}{literal}'));
            bloglist_{/literal}{$name}{literal}.find('.loader').html($('{/literal}{loading span=1 title="Loading Posts"|gettext}{literal}'));
        });

        // Watches the browser history for changes
        window.addEventListener('popstate', function(e) {
            state = History.getState();
            if (state.data.name == '{/literal}{$name}{literal}') {
                // moving to a new posts
                $.ajax({
                    type: "POST",
                    headers: { 'X-Transaction': 'Load Posts'},
                    url: sUrl_{/literal}{$name}{literal},
                    data: "page=" + state.data.rel,
                    success: handleSuccess_{/literal}{$name}{literal}
                });
                // bloglist_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Posts"|gettext}{literal}'));
                bloglist_{/literal}{$name}{literal}.find('.loader').html($('{/literal}{loading span=1 title="Loading Posts"|gettext}{literal}'));
            }
        });
    });
{/literal}
{/script}
{/if}
