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

<div class="module news showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{/if}
    {rss_link}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}</h1>{/if}

    {permissions}
    <div class="module-actions">
        {if $permissions.create == true}
            {icon class="add" action=edit rank=1 text="Add a news post"|gettext}
        {/if}
        {if $permissions.manage == 1}
            {if !$config.disabletags}
            |  {icon controller=expTag class="manage" action=manage_module model='news' text="Manage Tags"|gettext}
            {/if}
            {*{if $rank == 1}*}
            {if $config.order == 'rank'}
            |  {ddrerank items=$page->records model="news" label="News Items"|gettext}
            {/if}
        {/if}
        {if $permissions.showUnpublished == 1 }
             |  {icon class="view" action=showUnpublished text="View Expired/Unpublished News"|gettext}
        {/if}
    </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {subscribe_link}
    <div id="{$name}list">
        {include 'newslist.tpl'}
    </div>
</div>

{if $config.ajax_paging}
{script unique="`$name`listajax" yui3mods="1"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','io','node-event-delegate', function(Y) {
    var newslist = Y.one('#{/literal}{$name}{literal}list');
    var cfg = {
    			method: "POST",
    			headers: { 'X-Transaction': 'Load Newsitems'},
    			arguments : { 'X-Transaction': 'Load Newsitems'}
    		};

    src = '{/literal}{$__loc->src}{literal}';
	var sUrl = EXPONENT.PATH_RELATIVE+"index.php?controller=news&action=showall&view=newslist&ajax_action=1&src="+src;

	var handleSuccess = function(ioId, o){
//		Y.log(o.responseText);
		Y.log("The success handler was called.  Id: " + ioId + ".", "info", "newsitems nav");

        if(o.responseText){
            newslist.setContent(o.responseText);
            newslist.all('script').each(function(n){
                if(!n.get('src')){
                    eval(n.get('innerHTML'));
                } else {
                    var url = n.get('src');
                    if (url.indexOf("ckeditor")) {
                        Y.Get.script(url);
                    };
                };
            });
            newslist.all('link').each(function(n){
                var url = n.get('href');
                Y.Get.css(url);
            });
        } else {
            newslist.one('.loadingdiv').remove();
        }
	};

	//A function handler to use for failed requests:
	var handleFailure = function(ioId, o){
		Y.log("The failure handler was called.  Id: " + ioId + ".", "info", "newsitems nav");
	};

	//Subscribe our handlers to IO's global custom events:
	Y.on('io:success', handleSuccess);
	Y.on('io:failure', handleFailure);

    newslist.delegate('click', function(e){
        e.halt();
        cfg.data = "page="+e.currentTarget.get('rel');
        var request = Y.io(sUrl, cfg);
        newslist.setContent(Y.Node.create('<div class="loadingdiv">{/literal}{"Loading Items"|gettext}{literal}</div>'));
    }, 'a.pager');
});
{/literal}
{/script}
{/if}