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

{uniqueid prepend="portfolio" assign="name"}

{css unique="portfolio" link="`$asset_path`css/portfolio.css"}

{/css}

{if $config.usecategories}
{css unique="categories" corecss="categories"}

{/css}
{/if}

<div class="module portfolio showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit rank=1 title="Add to the top"|gettext text="Add a Portfolio Piece"|gettext}
			{/if}
            {if $permissions.manage}
                {icon class="downloadfile" action=export_csv text="Export as CSV"|gettext}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='portfolio' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='portfolio' text="Manage Categories"|gettext}
                {/if}
            {/if}
			{*{if $permissions.manage && $rank == 1}*}
			{if $permissions.manage && $config.order == 'rank'}
				{ddrerank items=$page->records model="portfolio" label="Portfolio Pieces"|gettext}
			{/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    <div id="{$name}list">
        {exp_include file='portfoliolist.tpl'}
    </div>
</div>

{if $smarty.const.AJAX_PAGING}
{if empty($params.page)}
    {$params.page = 1}
{/if}
{script unique="`$name`itemajax" jquery="jquery.history"}
{literal}
    $(document).ready(function() {
        var portfoliolist_{/literal}{$name}{literal} = $('#{/literal}{$name}{literal}list');
        var page_parm_{/literal}{$name}{literal} = '';
        if (EXPONENT.SEF_URLS) {
            page_parm_{/literal}{$name}{literal} = '/page/';
        } else {
            page_parm_{/literal}{$name}{literal} = '&page=';
        }
        var History = window.History;
        History.pushState({name:'{/literal}{$name}{literal}',rel:'{/literal}{$params.page}{literal}'});
        {/literal}
            {$orig_params = ['controller' => 'portfolio', 'action' => 'showall', 'src' => $params.src]}
        {literal}
        var orig_url_{/literal}{$name}{literal} = '{/literal}{makeLink($orig_params)}{literal}';
        var sUrl_{/literal}{$name}{literal} = EXPONENT.PATH_RELATIVE + "index.php?controller=portfolio&action=showall&view=portfoliolist&ajax_action=1&src={/literal}{$__loc->src}{literal}";

        // ajax load new items
        var handleSuccess_{/literal}{$name}{literal} = function(o, ioId){
            if(o){
                portfoliolist_{/literal}{$name}{literal}.html(o);
                portfoliolist_{/literal}{$name}{literal}.find('script').each(function(k, n){
                    if(!$(n).attr('src')){
                        eval($(n).html);
                    } else {
                        $.getScript($(n).attr('src'));
                    };
                });
                portfoliolist_{/literal}{$name}{literal}.find('link').each(function(k, n){
                    $("head").append("  <link href=\"" + $(n).attr('href') + "\" rel=\"stylesheet\" type=\"text/css\" />");
                });
            } else {
                $('#{/literal}{$name}{literal}item.loadingdiv').remove();
                portfoliolist_{/literal}{$name}{literal}.html('Unable to load content');
                portfoliolist_{/literal}{$name}{literal}.css('opacity', 1);
            }
        };

        portfoliolist_{/literal}{$name}{literal}.delegate('a.pager', 'click', function(e){
            e.preventDefault();
            History.pushState({name:'{/literal}{$name}{literal}', rel:$(this)[0].rel}, '{/literal}{'Portfoio'|gettext}{literal}', orig_url_{/literal}{$name}{literal} + page_parm_{/literal}{$name}{literal} + $(this)[0].rel);
            // moving to a new items
            $.ajax({
                type: "POST",
                headers: { 'X-Transaction': 'Load Items'},
                url: sUrl_{/literal}{$name}{literal},
                data: "page=" + $(this)[0].rel,
                success: handleSuccess_{/literal}{$name}{literal}
            });
            // portfoliolist_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Items"|gettext}{literal}'));
            portfoliolist_{/literal}{$name}{literal}.find('.loader').html($('{/literal}{loading span=1 title="Loading Items"|gettext}{literal}'));
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
                // portfoliolist_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Items"|gettext}{literal}'));
                portfoliolist_{/literal}{$name}{literal}.find('.loader').html($('{/literal}{loading span=1 title="Loading Items"|gettext}{literal}'));
            }
        });
    });
{/literal}
{/script}
{/if}
