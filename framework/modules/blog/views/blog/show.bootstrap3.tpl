{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

{if !empty($config.enable_facebook_like) || !empty($config.displayfbcomments)}
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v14.0&appId={$config.app_id}&autoLogAppEvents=1" nonce="9wKafjYh"></script>
{/if}

{if $config.enable_tweet}
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
{/if}

<div class="module blog show">
    <div id="{$name}item">
        {exp_include file='blogitem.tpl'}
    </div>
</div>

{if $smarty.const.AJAX_PAGING}
{script unique="`$name`itemajax" jquery="jquery.history"}
{literal}
    $(document).ready(function() {
        var blogitem_{/literal}{$name}{literal} = $('#{/literal}{$name}{literal}item');
        var page_parm_{/literal}{$name}{literal} = '';
        if (EXPONENT.SEF_URLS) {
            page_parm_{/literal}{$name}{literal} = '/title/';
        } else {
            page_parm_{/literal}{$name}{literal} = '&title=';
        }
        var History = window.History;
        History.pushState({name:'{/literal}{$name}{literal}',rel:'{/literal}{$params.title}{literal}'});
        {/literal}
            {$orig_params = ['controller' => 'blog', 'action' => 'show']}
        {literal}
        var orig_url_{/literal}{$name}{literal} = '{/literal}{makeLink($orig_params)}{literal}';
        var sUrl_{/literal}{$name}{literal} = EXPONENT.PATH_RELATIVE + "index.php?controller=blog&action=show&view=blogitem&ajax_action=1&src={/literal}{$__loc->src}{literal}";

        // ajax load new post
        var handleSuccess_{/literal}{$name}{literal} = function(o, ioId){
            if(o){
                blogitem_{/literal}{$name}{literal}.html(o);
                blogitem_{/literal}{$name}{literal}.find('script').each(function(k, n){
                    if(!$(n).attr('src')){
                        eval($(n).html);
                    } else {
                        $.getScript($(n).attr('src'));
                    };
                });
                blogitem_{/literal}{$name}{literal}.find('link').each(function(k, n){
                    $("head").append("  <link href=\"" + $(n).attr('href') + "\" rel=\"stylesheet\" type=\"text/css\" />");
                });
                if (document.getElementsByClassName('fb-like').length) {
                    FB.XFBML.parse();
                }
                if (document.getElementsByClassName('twitter-share-button').length) {
                    twttr.widgets.load();
                }
                $title = blogitem_{/literal}{$name}{literal}.find('div.item .heading');
                document.title = $title[0].outerText.trim();
            } else {
                $('#{/literal}{$name}{literal}item.loadingdiv').remove();
                blogitem_{/literal}{$name}{literal}.html('Unable to load content');
                blogitem_{/literal}{$name}{literal}.css('opacity', 1);
            }
        };

        blogitem_{/literal}{$name}{literal}.delegate('a.blognav', 'click', function(e){
            e.preventDefault();
            History.pushState({name:'{/literal}{$name}{literal}', rel:$(this)[0].rel}, $(this)[0].title.trim(), orig_url_{/literal}{$name}{literal} + page_parm_{/literal}{$name}{literal} + $(this)[0].rel);
            // moving to a new post
            $.ajax({
                type: "POST",
                headers: { 'X-Transaction': 'Load Post'},
                url: sUrl_{/literal}{$name}{literal},
                data: "title=" + $(this)[0].rel,
                success: handleSuccess_{/literal}{$name}{literal}
            });
            // blogitem_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Post"|gettext}{literal}'));
            blogitem_{/literal}{$name}{literal}.find('.loader').html($('{/literal}{loading span=1 title="Loading Post"|gettext}{literal}'));
        });

        // Watches the browser history for changes
        window.addEventListener('popstate', function(e) {
            state = History.getState();
            if (state.data.name == '{/literal}{$name}{literal}') {
                // moving to a new post
                $.ajax({
                    type: "POST",
                    headers: { 'X-Transaction': 'Load Post'},
                    url: sUrl_{/literal}{$name}{literal},
                    data: "title=" + state.data.rel,
                    success: handleSuccess_{/literal}{$name}{literal}
                });
                // blogitem_{/literal}{$name}{literal}.html($('{/literal}{loading title="Loading Post"|gettext}{literal}'));
                blogitem_{/literal}{$name}{literal}.find('.loader').html($('{/literal}{loading span=1 title="Loading Post"|gettext}{literal}'));
            }
        });
    });
{/literal}
{/script}
{/if}
