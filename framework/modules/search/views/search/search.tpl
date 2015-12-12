{*
 * Copyright (c) 2004-2015 OIC Group, Inc.
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
{script unique="`$name`listajax" yui3mods="node,io,node-event-delegate" jquery="jquery.history"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    var searchlist = Y.one('#{/literal}{$name}{literal}list');
    var page_parm = '';
    if (EXPONENT.SEF_URLS) {
        page_parm = '/page/';
    } else {
        page_parm = '&page=';
    }
    var History = window.History;
    History.pushState({name:'{/literal}{$name}{literal}',rel:'{/literal}{$params.page}{literal}'});
    var orig_url = '{/literal}{$params.page = ''}{$params.moduletitle = ''}{$params.view = ''}{makeLink($params)}{literal}';
    var cfg = {
    			method: "POST",
    			headers: { 'X-Transaction': 'Load searchitems'},
    			arguments : { 'X-Transaction': 'Load searchitems'}
    		};

    src = '{/literal}{$__loc->src}{literal}';
	var sUrl = EXPONENT.PATH_RELATIVE+"index.php?controller=search&action=search&view=searchlist&ajax_action=1&src="+src + "&search_string={/literal}{$terms|urlencode}{literal}";

	var handleSuccess = function(ioId, o){
        if(o.responseText){
            searchlist.setContent(o.responseText);
            searchlist.all('script').each(function(n){
                if(!n.get('src')){
                    eval(n.get('innerHTML'));
                } else {
                    var url = n.get('src');
                    Y.Get.script(url);
                };
            });
                searchlist.all('link').each(function(n){
                var url = n.get('href');
                Y.Get.css(url);
            });
        } else {
            searchlist.one('.loadingdiv').remove();
        }
	};

	//A function handler to use for failed requests:
	var handleFailure = function(ioId, o){
		Y.log("The failure handler was called.  Id: " + ioId + ".", "info", "Searchitems nav");
	};

	//Subscribe our handlers to IO's global custom events:
	Y.on('io:success', handleSuccess);
	Y.on('io:failure', handleFailure);

    searchlist.delegate('click', function(e){
        e.halt();
        History.pushState({name:'{/literal}{$name}{literal}',rel:e.currentTarget.get('rel')}, 'Title', orig_url+page_parm+e.currentTarget.get('rel'));
        cfg.data = "page="+e.currentTarget.get('rel');
        var request = Y.io(sUrl, cfg);
//        searchlist.setContent(Y.Node.create('<div class="loadingdiv">{/literal}{"Searching"|gettext}{literal}</div>'));
        searchlist.setContent(Y.Node.create('{/literal}{loading title="Searching"|gettext}{literal}'));
    }, 'a.pager');

    // Watches the browser history for changes
    window.addEventListener('popstate', function(e) {
        state = History.getState()
        if (state.data.name == '{/literal}{$name}{literal}') {
            // moving to a new page
            cfg.data = "page="+state.data.rel;
            var request = Y.io(sUrl, cfg);
//            searchlist.setContent(Y.Node.create('<div class="loadingdiv">{/literal}{"Searching"|gettext}{literal}</div>'));
            searchlist.setContent(Y.Node.create('{/literal}{loading title="Searching"|gettext}{literal}'));
        }
    });
});
{/literal}
{/script}
{/if}
