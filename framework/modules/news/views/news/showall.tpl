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

<div class="module news showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{/if}
    {rss_link}
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}

    {permissions}
    <div class="module-actions">
        {if $permissions.create}
            {icon class="add" action=edit rank=1 text="Add a news post"|gettext}
        {/if}
        {if $permissions.manage}
            {if !$config.disabletags}
            |  {icon controller=expTag class="manage" action=manage_module model='news' text="Manage Tags"|gettext}
            {/if}
            {*{if $rank == 1}*}
            {if $config.order == 'rank'}
            |  {ddrerank items=$page->records model="news" label="News Items"|gettext}
            {/if}
        {/if}
        {if $permissions.showUnpublished}
             |  {icon class="view" action=showUnpublished text="View Expired/Unpublished News"|gettext}
        {/if}
    </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {subscribe_link}
    <div id="{$name}list">
        {exp_include file='newslist.tpl'}
    </div>
</div>

{if $smarty.const.AJAX_PAGING}
{if empty($params.page)}
    {$params.page = 1}
{/if}
{script unique="`$name`listajax" yui3mods="node,io,node-event-delegate" jquery="jquery.history"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    var newslist = Y.one('#{/literal}{$name}{literal}list');
    var page_parm = '';
    if (EXPONENT.SEF_URLS) {
        page_parm = '/page/';
    } else {
        page_parm = '&page=';
    }
//    var newslisthistory = new Y.History();
//    newslisthistory.addValue('newspage',{/literal}{$params.page}{literal});
    var History = window.History;
    History.pushState({name:'{/literal}{$name}{literal}',rel:'{/literal}{$params.page}{literal}'});
    {/literal}
        {$orig_params = ['controller' => 'news', 'action' => 'showall', 'src' => $params.src]}
    {literal}
    var orig_url = '{/literal}{makeLink($orig_params)}{literal}';
    var cfg = {
    			method: "POST",
    			headers: { 'X-Transaction': 'Load Newsitems'},
    			arguments : { 'X-Transaction': 'Load Newsitems'}
    		};

	var sUrl = EXPONENT.PATH_RELATIVE+"index.php?controller=news&action=showall&view=newslist&ajax_action=1&src={/literal}{$__loc->src}{literal}";

	var handleSuccess = function(ioId, o){
        if(o.responseText){
            newslist.setContent(o.responseText);
            newslist.all('script').each(function(n){
                if(!n.get('src')){
                    eval(n.get('innerHTML'));
                } else {
                    Y.Get.script(n.get('src'));
                };
            });
            newslist.all('link').each(function(n){
                Y.Get.css(n.get('href'));
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
        History.pushState({name:'{/literal}{$name}{literal}',rel:e.currentTarget.get('rel')}, '{/literal}{'News Items'|gettext}{literal}', orig_url+page_parm+e.currentTarget.get('rel'));
        cfg.data = "page="+e.currentTarget.get('rel');
        var request = Y.io(sUrl, cfg);
        newslist.setContent(Y.Node.create('{/literal}{loading title="Loading Items"|gettext}{literal}'));
    }, 'a.pager');

    // Watches the browser history for changes
    window.addEventListener('popstate', function(e) {
        state = History.getState()
        if (state.data.name == '{/literal}{$name}{literal}') {
            // moving to a new page
            cfg.data = "page="+state.data.rel;
            var request = Y.io(sUrl, cfg);
            newslist.setContent(Y.Node.create('{/literal}{loading title="Loading Items"|gettext}{literal}'));
        }
    });
});
{/literal}
{/script}
{/if}