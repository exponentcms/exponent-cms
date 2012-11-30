{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

<div class="module news show">
    <div id="newsitem">
        {include 'newsitem.tpl'}
    </div>
</div>

{script unique="newsitemajax" yui3mods="1"}
{literal}

YUI(EXPONENT.YUI3_CONFIG).use('node','io','node-event-delegate', function(Y) {
    var newsitem = Y.one('#newsitem');
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
        } else {
            Y.one('#newsitem.loadingdiv').remove();
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
        cfg.data = "title="+e.currentTarget.get('rel');
        var request = Y.io(sUrl, cfg);
        newsitem.setContent(Y.Node.create('<div class="loadingdiv">{/literal}{"Loading Item"|gettext}{literal}</div>'));
    }, 'a.nav');
});
{/literal}
{/script}
