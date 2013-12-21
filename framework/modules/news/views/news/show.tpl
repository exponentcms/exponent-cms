{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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
        {include 'newsitem.tpl'}
    </div>
</div>

{if !empty($config.ajax_paging)}
{script unique="`$name`itemajax" yui3mods="1"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','io','node-event-delegate', function(Y) {
    var newsitem = Y.one('#{/literal}{$name}{literal}item');
    var cfg = {
    			method: "POST",
    			headers: { 'X-Transaction': 'Load Newsitem'},
    			arguments : { 'X-Transaction': 'Load Newsitem'}
    		};

    src = '{/literal}{$__loc->src}{literal}';
	var sUrl = EXPONENT.PATH_RELATIVE+"index.php?controller=news&action=show&view=newsitem&ajax_action=1&src="+src;

	var handleSuccess = function(ioId, o){
//		Y.log(o.responseText);
		Y.log("The success handler was called.  Id: " + ioId + ".", "info", "newsitem nav");

        if(o.responseText){
            newsitem.setContent(o.responseText);
            newsitem.all('script').each(function(n){
                if(!n.get('src')){
                    eval(n.get('innerHTML'));
                } else {
                    var url = n.get('src');
                    if (url.indexOf("ckeditor")) {
                        Y.Get.script(url);
                    };
                };
            });
            newsitem.all('link').each(function(n){
                var url = n.get('href');
                Y.Get.css(url);
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
        cfg.data = "title="+e.currentTarget.get('rel');
        var request = Y.io(sUrl, cfg);
        newsitem.setContent(Y.Node.create('<div class="loadingdiv">{/literal}{"Loading Item"|gettext}{literal}</div>'));
    }, 'a.nav');
});
{/literal}
{/script}
{/if}
