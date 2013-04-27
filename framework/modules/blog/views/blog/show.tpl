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

{uniqueid prepend="blog" assign="name"}

{css unique="blog" link="`$asset_path`css/blog.css"}

{/css}

<div class="module blog show">
    <div id="{$name}item">
        {include 'blogitem.tpl'}
    </div>
</div>

{if $config.ajax_paging}
{script unique="`$name`itemajax" yui3mods="1"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','io','node-event-delegate', function(Y) {
    var blogitem = Y.one('#{/literal}{$name}{literal}item');
    var cfg = {
    			method: "POST",
    			headers: { 'X-Transaction': 'Load Blogitem'},
    			arguments : { 'X-Transaction': 'Load Blogitem'}
    		};

    src = '{/literal}{$__loc->src}{literal}';
	var sUrl = EXPONENT.PATH_RELATIVE+"index.php?controller=blog&action=show&view=blogitem&ajax_action=1&src="+src;

	var handleSuccess = function(ioId, o){
//		Y.log(o.responseText);
		Y.log("The success handler was called.  Id: " + ioId + ".", "info", "blogitem nav");

        if(o.responseText){
            blogitem.setContent(o.responseText);
            blogitem.all('script').each(function(n){
                if(!n.get('src')){
                    eval(n.get('innerHTML'));
                } else {
                    var url = n.get('src');
                    if (url.indexOf("ckeditor")) {
                        Y.Get.script(url);
                    };
                };
            });
            blogitem.all('link').each(function(n){
                var url = n.get('href');
                Y.Get.css(url);
            });
        } else {
            blogitem.one('.loadingdiv').remove();
        }
	};

	//A function handler to use for failed requests:
	var handleFailure = function(ioId, o){
		Y.log("The failure handler was called.  Id: " + ioId + ".", "info", "blogitem nav");
	};

	//Subscribe our handlers to IO's global custom events:
	Y.on('io:success', handleSuccess);
	Y.on('io:failure', handleFailure);

    blogitem.delegate('click', function(e){
        e.halt();
        cfg.data = "title="+e.currentTarget.get('rel');
        var request = Y.io(sUrl, cfg);
        blogitem.setContent(Y.Node.create('<div class="loadingdiv">{/literal}{"Loading Post"|gettext}{literal}</div>'));
    }, 'a.nav');
});
{/literal}
{/script}
{/if}