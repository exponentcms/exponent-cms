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

{uniqueid prepend="blog" assign="name"}

{css unique="blog" link="`$asset_path`css/blog.css"}

{/css}
{if $config.usecategories}
{css unique="categories" corecss="categories"}

{/css}
{/if}

<div class="module blog showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{/if}
    {rss_link}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit text="Add a new blog article"|gettext}
			{/if}
            {if $permissions.manage}
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
    <div id="{$name}list">
        {exp_include file='bloglist.tpl'}
    </div>
</div>

{if $smarty.const.AJAX_PAGING}
{if empty($params.page)}
    {$params.page = 1}
{/if}
{script unique="`$name`listajax" yui3mods="node,io,node-event-delegate" jquery="jquery.history"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    var bloglist = Y.one('#{/literal}{$name}{literal}list');
    var page_parm = '';
    if (EXPONENT.SEF_URLS) {
        page_parm = '/page/';
    } else {
        page_parm = '&page=';
    }
    var History = window.History;
    History.pushState({name:'{/literal}{$name}{literal}',rel:'{/literal}{$params.page}{literal}'});
    {/literal}
        {$orig_params = ['controller' => 'blog', 'action' => 'showall', 'src' => $params.src]}
    {literal}
    var orig_url = '{/literal}{makeLink($orig_params)}{literal}';
    var cfg = {
    			method: "POST",
    			headers: { 'X-Transaction': 'Load Blogitems'},
    			arguments : { 'X-Transaction': 'Load Blogitems'}
    		};

	var sUrl = EXPONENT.PATH_RELATIVE+"index.php?controller=blog&action=showall&view=bloglist&ajax_action=1&src={/literal}{$__loc->src}{literal}";
	var handleSuccess = function(ioId, o){
        if(o.responseText){
            bloglist.setContent(o.responseText);
            bloglist.all('script').each(function(n){
                if(!n.get('src')){
                    eval(n.get('innerHTML'));
                } else {
                    Y.Get.script(n.get('src'));
                };
            });
            bloglist.all('link').each(function(n){
                Y.Get.css(n.get('href'));
            });
        } else {
            bloglist.one('.loadingdiv').remove();
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
        History.pushState({name:'{/literal}{$name}{literal}',rel:e.currentTarget.get('rel')}, '{/literal}{'Blog Posts'|gettext}{literal}', orig_url+page_parm+e.currentTarget.get('rel'));
        cfg.data = "page="+e.currentTarget.get('rel');
        var request = Y.io(sUrl, cfg);
        bloglist.setContent(Y.Node.create('{/literal}{loading title="Loading Posts"|gettext}{literal}'));
    }, 'a.pager');

    // Watches the browser history for changes
    window.addEventListener('popstate', function(e) {
        state = History.getState()
        if (state.data.name == '{/literal}{$name}{literal}') {
            // moving to a new page
            cfg.data = "page="+state.data.rel;
            var request = Y.io(sUrl, cfg);
            bloglist.setContent(Y.Node.create('{/literal}{loading title="Loading Posts"|gettext}{literal}'));
        }
    });
});
{/literal}
{/script}
{/if}
