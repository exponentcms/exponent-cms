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

{uniqueid prepend="search" assign="name"}
{css unique="searchform" link="`$asset_path`css/search.css"}

{/css}

<div class="module search search-results">
	<h1>{'Search Results'|gettext}</h1>
    <div id="{$name}list">
        {exp_include file='searchlist.tpl'}
    </div>
</div>

{if $smarty.const.AJAX_PAGING}
{if empty($params.page)}
    {$params.page = 1}
{/if}
{script unique="`$name`itemajax" jquery="jquery.history"}
{literal}
    $(document).ready(function() {
        var searchlist_{/literal}{$name}{literal} = $('#{/literal}{$name}{literal}list');
        var page_parm_{/literal}{$name}{literal} = '';
        if (EXPONENT.SEF_URLS) {
            page_parm_{/literal}{$name}{literal} = '/page/';
        } else {
            page_parm_{/literal}{$name}{literal} = '&page=';
        }
        var History = window.History;
        History.pushState({name:'{/literal}{$name}{literal}',rel:'{/literal}{$params.page}{literal}'});
        {/literal}
            {$orig_params = ['controller' => 'search', 'action' => 'search', 'search_string' => $params.search_string]}
        {literal}
        var orig_url_{/literal}{$name}{literal} = '{/literal}{makeLink($orig_params)}{literal}';
        var sUrl_{/literal}{$name}{literal} = EXPONENT.PATH_RELATIVE + "index.php?controller=search&action=search&view=searchlist&ajax_action=1&search_string={/literal}{$terms|urlencode}{literal}";

        // ajax load new items
        var handleSuccess_{/literal}{$name}{literal} = function(o, ioId){
            if(o){
                searchlist_{/literal}{$name}{literal}.html(o);
                searchlist_{/literal}{$name}{literal}.find('script').each(function(k, n){
                    if(!$(n).attr('src')){
                        eval($(n).html);
                    } else {
                        $.getScript($(n).attr('src'));
                    };
                });
                searchlist_{/literal}{$name}{literal}.find('link').each(function(k, n){
                    $("head").append("  <link href=\"" + $(n).attr('href') + "\" rel=\"stylesheet\" type=\"text/css\" />");
                });
            } else {
                $('#{/literal}{$name}{literal}item.loadingdiv').remove();
                searchlist_{/literal}{$name}{literal}.html('Unable to load content');
                searchlist_{/literal}{$name}{literal}.css('opacity', 1);
            }
        };

        searchlist_{/literal}{$name}{literal}.delegate('a.pager', 'click', function(e){
            e.preventDefault();
            History.pushState({name:'{/literal}{$name}{literal}', rel:$(this)[0].rel}, '{/literal}{'Searching'|gettext}{literal}', orig_url_{/literal}{$name}{literal} + page_parm_{/literal}{$name}{literal} + $(this)[0].rel);
            // moving to a new items
            $.ajax({
                type: "POST",
                headers: { 'X-Transaction': 'Load Items'},
                url: sUrl_{/literal}{$name}{literal},
                data: "page=" + $(this)[0].rel,
                success: handleSuccess_{/literal}{$name}{literal}
            });
            searchlist_{/literal}{$name}{literal}.find('.loader').html($('{/literal}{loading span=1 title="Searching"|gettext}{literal}'));
        });

        // Watches the browser history for changes
        window.addEventListener('popstate', function(e) {
            state = History.getState();
            if (state.data.name == '{/literal}{$name}{literal}') {
                // moving to a new search hits
                $.ajax({
                    type: "POST",
                    headers: { 'X-Transaction': 'Searching'},
                    url: sUrl_{/literal}{$name}{literal},
                    data: "page=" + state.data.rel,
                    success: handleSuccess_{/literal}{$name}{literal}
                });
                searchlist_{/literal}{$name}{literal}.find('.loader').html($('{/literal}{loading span=1 title="Searching"|gettext}{literal}'));
            }
        });
    });
{/literal}
{/script}
{/if}
