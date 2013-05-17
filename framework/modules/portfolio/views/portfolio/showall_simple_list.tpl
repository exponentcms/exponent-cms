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

{uniqueid prepend="portfolio" assign="name"}

{css unique="portfolio" link="`$asset_path`css/portfolio.css"}

{/css}

{if $config.usecategories}
{css unique="categories" corecss="categories"}

{/css}
{/if}

<div class="module portfolio showall-simple-list">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle}</h1>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit rank=1 title="Add to the top"|gettext text="Add a Portfolio Piece"|gettext}
			{/if}
            {if $permissions.manage == 1}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='portfolio' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='portfolio' text="Manage Categories"|gettext}
                {/if}
            {/if}
			{*{if $permissions.manage == 1 && $rank == 1}*}
			{if $permissions.manage == 1 && $config.order == 'rank'}
				{ddrerank items=$page->records model="portfolio" label="Portfolio Pieces"|gettext}
			{/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    <div id="{$name}list">
        {include 'portfoliolist_simple.tpl'}
    </div>
</div>

{if $config.ajax_paging}
{script unique="`$name`listajax" yui3mods="1"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','io','node-event-delegate', function(Y) {
    var portfoliolist = Y.one('#{/literal}{$name}{literal}list');
    var cfg = {
    			method: "POST",
    			headers: { 'X-Transaction': 'Load Portfolioitems'},
    			arguments : { 'X-Transaction': 'Load Portfolioitems'}
    		};

    src = '{/literal}{$__loc->src}{literal}';
	var sUrl = EXPONENT.PATH_RELATIVE+"index.php?controller=portfolio&action=showall&view=portfoliolist_simple&ajax_action=1&src="+src;

	var handleSuccess = function(ioId, o){
//		Y.log(o.responseText);
		Y.log("The success handler was called.  Id: " + ioId + ".", "info", "portfolioitems nav");

        if(o.responseText){
                portfoliolist.setContent(o.responseText);
                portfoliolist.all('script').each(function(n){
                if(!n.get('src')){
                    eval(n.get('innerHTML'));
                } else {
                    var url = n.get('src');
                    if (url.indexOf("ckeditor")) {
                        Y.Get.script(url);
                    };
                };
            });
            portfoliolist.all('link').each(function(n){
                var url = n.get('href');
                Y.Get.css(url);
            });
        } else {
            portfoliolist.one('.loadingdiv').remove();
        }
	};

	//A function handler to use for failed requests:
	var handleFailure = function(ioId, o){
		Y.log("The failure handler was called.  Id: " + ioId + ".", "info", "portfolioitems nav");
	};

	//Subscribe our handlers to IO's global custom events:
	Y.on('io:success', handleSuccess);
	Y.on('io:failure', handleFailure);

    portfoliolist.delegate('click', function(e){
        e.halt();
        cfg.data = "page="+e.currentTarget.get('rel');
        var request = Y.io(sUrl, cfg);
        portfoliolist.setContent(Y.Node.create('<div class="loadingdiv">{/literal}{"Loading Portfolios"|gettext}{literal}</div>'));
    }, 'a.pager');
});
{/literal}
{/script}
{/if}
