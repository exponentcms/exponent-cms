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

<div class="module news show">
    <div id="{$name}item">
        {exp_include file='newsitem.tpl'}
    </div>
</div>

{if $smarty.const.AJAX_PAGING}
{script unique="`$name`itemajax" jquery="jquery.history"}
{literal}
    $(document).ready(function() {
        var newsitem_{/literal}{$name}{literal} = $('#{/literal}{$name}{literal}item');
        var page_parm_{/literal}{$name}{literal} = '';
        if (EXPONENT.SEF_URLS) {
            page_parm_{/literal}{$name}{literal} = '/title/';
        } else {
            page_parm_{/literal}{$name}{literal} = '&title=';
        }
        var History = window.History;
        History.pushState({name:'{/literal}{$name}{literal}',rel:'{/literal}{$params.title}{literal}'});
        {/literal}
            {$orig_params = ['controller' => 'news', 'action' => 'show']}
        {literal}
        var orig_url_{/literal}{$name}{literal} = '{/literal}{makeLink($orig_params)}{literal}';
        var sUrl_{/literal}{$name}{literal} = EXPONENT.PATH_RELATIVE + "index.php?controller=news&action=show&view=newsitem&ajax_action=1&src={/literal}{$__loc->src}{literal}";

        // ajax load new item
        var handleSuccess_{/literal}{$name}{literal} = function(o, ioId){
            if(o){
                newsitem_{/literal}{$name}{literal}.html(o);
                newsitem_{/literal}{$name}{literal}.find('script').each(function(k, n){
                    if(!$(n).attr('src')){
                        eval($(n).html);
                    } else {
                        $.getScript($(n).attr('src'));
                    };
                });
                newsitem_{/literal}{$name}{literal}.find('link').each(function(k, n){
                    $("head").append("  <link href=\"" + $(n).attr('href') + "\" rel=\"stylesheet\" type=\"text/css\" />");
                });
            } else {
                $('#{/literal}{$name}{literal}item.loadingdiv').remove();
                newsitem_{/literal}{$name}{literal}.html('Unable to load content');
                newsitem_{/literal}{$name}{literal}.css('opacity', 1);
            }
        };

        newsitem_{/literal}{$name}{literal}.delegate('a.newsnav', 'click', function(e){
            e.preventDefault();
            History.pushState({name:'{/literal}{$name}{literal}', rel:$(this)[0].rel}, $(this)[0].title.trim(), orig_url_{/literal}{$name}{literal} + page_parm_{/literal}{$name}{literal} + $(this)[0].rel);
            // moving to a new item
            $.ajax({
                type: "POST",
                headers: { 'X-Transaction': 'Load Item'},
                url: sUrl_{/literal}{$name}{literal},
                data: "title=" + $(this)[0].rel,
                success: handleSuccess_{/literal}{$name}{literal}
            });
            // newsitem_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Item"|gettext}{literal}'));
            newsitem_{/literal}{$name}{literal}.find('.loader').html($('{/literal}{loading span=1 title="Loading Item"|gettext}{literal}'));
        });

        // Watches the browser history for changes
        window.addEventListener('popstate', function(e) {
            state = History.getState();
            if (state.data.name == '{/literal}{$name}{literal}') {
                // moving to a new item
                $.ajax({
                    type: "POST",
                    headers: { 'X-Transaction': 'Load Item'},
                    url: sUrl_{/literal}{$name}{literal},
                    data: "title=" + state.data.rel,
                    success: handleSuccess_{/literal}{$name}{literal}
                });
                // blogitem_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Item"|gettext}{literal}'));
                blogitem_{/literal}{$name}{literal}.find('.loader').html($('{/literal}{loading span=1 title="Loading Item"|gettext}{literal}'));
            }
        });
    });
{/literal}
{/script}
{/if}
