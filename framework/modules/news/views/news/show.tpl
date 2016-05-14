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
{script unique="`$name`itemajax" yui3mods="node,io,node-event-delegate" jquery="jquery.history"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    var newsitem = Y.one('#{/literal}{$name}{literal}item');
    var page_parm = '';
    if (EXPONENT.SEF_URLS) {
        page_parm = '/title/';
    } else {
        page_parm = '&title=';
    }
    var History = window.History;
    History.pushState({name:'{/literal}{$name}{literal}',rel:'{/literal}{$params.title}{literal}'});
    {/literal}
        {$orig_params = ['controller' => 'news', 'action' => 'show']}
    {literal}
    var orig_url = '{/literal}{makeLink($orig_params)}{literal}';
    var cfg = {
    			method: "POST",
    			headers: { 'X-Transaction': 'Load Newsitem'},
    			arguments : { 'X-Transaction': 'Load Newsitem'}
    		};
	var sUrl = EXPONENT.PATH_RELATIVE+"index.php?controller=news&action=show&view=newsitem&ajax_action=1&src={/literal}{$__loc->src}{literal}";

	var handleSuccess = function(ioId, o){
        if(o.responseText){
            newsitem.setContent(o.responseText);
            newsitem.all('script').each(function(n){
                if(!n.get('src')){
                    eval(n.get('innerHTML'));
                } else {
                    Y.Get.script(n.get('src'));
                };
            });
            newsitem.all('link').each(function(n){
                Y.Get.css(n.get('href'));
            });
        } else {
            newsitem.one('.loadingdiv').remove();
        }
	};

	//A function handler to use for failed requests:
	var handleFailure = function(ioId, o){
		Y.log("The failure handler was called.  Id: " + ioId + ".", "info", "newsitem nav");
	};

	//Subscribe our handlers to IO's global custom events:
	Y.on('io:success', handleSuccess);
	Y.on('io:failure', handleFailure);

    newsitem.delegate('click', function(e){
        e.halt();
        History.pushState({name:'{/literal}{$name}{literal}',rel:e.currentTarget.get('rel')}, e.currentTarget.get('text').trim(), orig_url+page_parm+e.currentTarget.get('rel'));
        cfg.data = "title="+e.currentTarget.get('rel');
        var request = Y.io(sUrl, cfg);
        newsitem.setContent(Y.Node.create('{/literal}{loading title="Loading Item"|gettext}{literal}'));
    }, 'a.newsnav');

    // Watches the browser history for changes
    window.addEventListener('popstate', function(e) {
        state = History.getState()
        if (state.data.name == '{/literal}{$name}{literal}') {
            // moving to a new item
            cfg.data = "title="+state.data.rel;
            var request = Y.io(sUrl, cfg);
            newsitem.setContent(Y.Node.create('{/literal}{loading title="Loading Item"|gettext}{literal}'));
        }
    });
});
{/literal}
{/script}
{/if}
