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

{css unique="blog" link="`$asset_path`css/blog.css"}

{/css}
{if $config.usecategories}
{css unique="categories" corecss="categories"}

{/css}
{/if}

<div class="module blog showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{/if}
    {rss_link}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}</h1>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.edit == 1}
				{icon class=add action=edit text="Add a new blog article"|gettext}
			{/if}
            {if $permissions.manage == 1}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='blog' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='blog' text="Manage Categories"|gettext}
                {/if}
            {/if}
		</div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {subscribe_link}
    <div id="bloglist">
        {include 'bloglist.tpl'}
    </div>
</div>

{script unique="bloglistajax" yui3mods="1"}
{literal}

YUI(EXPONENT.YUI3_CONFIG).use('node','io','node-event-delegate', function(Y) {
    var bloglist = Y.one('#bloglist');
    var cfg = {
    			method: "POST",
    			headers: { 'X-Transaction': 'Load Blogitems'},
    			arguments : { 'X-Transaction': 'Load Blogitems'}
    		};

    src = '{/literal}{$__loc->src}{literal}';
	var sUrl = EXPONENT.PATH_RELATIVE+"index.php?controller=blog&action=showall&view=bloglist&ajax_action=1&src="+src;

	var handleSuccess = function(ioId, o){
//		Y.log(o.responseText);
		Y.log("The success handler was called.  Id: " + ioId + ".", "info", "blogitems nav");

        if(o.responseText){
            bloglist.setContent(o.responseText);
            bloglist.all('script').each(function(n){
                if(!n.get('src')){
                    eval(n.get('innerHTML'));
                } else {
                    var url = n.get('src');
                    if (url.indexOf("ckeditor")) {
                        Y.Get.script(url);
                    };
                };
            });
            bloglist.all('link').each(function(n){
                var url = n.get('href');
                Y.Get.css(url);
            });
        } else {
            Y.one('#bloglist.loadingdiv').remove();
        }
	};

	//A function handler to use for failed requests:
	var handleFailure = function(ioId, o){
		Y.log("The failure handler was called.  Id: " + ioId + ".", "info", "blogitems nav");
	};

	//Subscribe our handlers to IO's global custom events:
	Y.on('io:success', handleSuccess);
	Y.on('io:failure', handleFailure);

    bloglist.delegate('click', function(e){
        e.halt();
        cfg.data = "page="+e.currentTarget.get('rel');
        var request = Y.io(sUrl, cfg);
        bloglist.setContent(Y.Node.create('<div class="loadingdiv">{/literal}{"Loading Posts"|gettext}{literal}</div>'));
    }, 'a.pager');
});
{/literal}
{/script}
