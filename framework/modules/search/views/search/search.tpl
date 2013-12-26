{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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
        {include 'searchlist.tpl'}
    </div>
</div>

{if $config.ajax_paging}
{script unique="`$name`listajax" yui3mods="1"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','io','node-event-delegate', function(Y) {
    var searchlist = Y.one('#{/literal}{$name}{literal}list');
    var cfg = {
    			method: "POST",
    			headers: { 'X-Transaction': 'Load searchitems'},
    			arguments : { 'X-Transaction': 'Load searchitems'}
    		};

    src = '{/literal}{$__loc->src}{literal}';
	var sUrl = EXPONENT.PATH_RELATIVE+"index.php?controller=search&action=search&view=searchlist&ajax_action=1&src="+src + "&search_string={/literal}{$terms|urlencode}{literal}";

	var handleSuccess = function(ioId, o){
//		Y.log(o.responseText);
		Y.log("The success handler was called.  Id: " + ioId + ".", "info", "Searchitems nav");

        if(o.responseText){
            searchlist.setContent(o.responseText);
            searchlist.all('script').each(function(n){
                if(!n.get('src')){
                    eval(n.get('innerHTML'));
                } else {
                    var url = n.get('src');
                    if (url.indexOf("ckeditor")) {
                        Y.Get.script(url);
                    };
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
        cfg.data = "page="+e.currentTarget.get('rel');
        var request = Y.io(sUrl, cfg);
        searchlist.setContent(Y.Node.create('<div class="loadingdiv">{/literal}{"Searching"|gettext}{literal}</div>'));
    }, 'a.pager');
});
{/literal}
{/script}
{/if}
