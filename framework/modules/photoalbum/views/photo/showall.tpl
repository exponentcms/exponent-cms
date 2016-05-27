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

{uniqueid prepend="gallery" assign="name"}

{css unique="photo-album" link="`$asset_path`css/photoalbum.css"}

{/css}

{$rel}
<div class="module photoalbum showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit rank=1 title="Add to the top"|gettext text="Add Image"|gettext}
                {icon class=add action=multi_add title="Quickly Add Many Images"|gettext text="Add Multiple Images"|gettext}
			{/if}
            {if $permissions.delete}
                {icon class=delete action=delete_multi title="Delete Many Images"|gettext text="Delete Multiple Images"|gettext onclick='null;'}
            {/if}
            {if $permissions.manage}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='photo' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='photo' text="Manage Categories"|gettext}
                {/if}
                {if $config.order == 'rank'}
                    {ddrerank items=$page->records model="photo" label="Images"|gettext}
                {/if}
            {/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    <div id="{$name}list">
        {exp_include file='photolist.tpl'}
    </div>
</div>

{if $config.lightbox}
{script unique="shadowbox" yui3mods="gallery-lightbox"}
{literal}
    EXPONENT.YUI3_CONFIG.modules = {
       'gallery-lightbox' : {
           fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/common/assets/js/gallery-lightbox.js',
           requires : ['base','node','anim','selector-css3','lightbox-css']
       },
       'lightbox-css': {
           fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/common/assets/css/gallery-lightbox.css',
           type: 'css'
       }
    }

    YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
        Y.Lightbox.init();
    });
{/literal}
{/script}
{/if}

{if $smarty.const.AJAX_PAGING}
{if empty($params.page)}
    {$params.page = 1}
{/if}
{script unique="`$name`listajax" yui3mods="node,io,node-event-delegate" jquery="jquery.history"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    var photolist = Y.one('#{/literal}{$name}{literal}list');
    var page_parm = '';
    if (EXPONENT.SEF_URLS) {
        page_parm = '/page/';
    } else {
        page_parm = '&page=';
    }
    var History = window.History;
    History.pushState({name:'{/literal}{$name}{literal}',rel:'{/literal}{$params.page}{literal}'});
        {/literal}
        {$orig_params = ['controller' => 'photo', 'action' => 'showall', 'src' => $params.src]}
    {literal}
    var orig_url = '{/literal}{makeLink($orig_params)}{literal}';
    var cfg = {
    			method: "POST",
    			headers: { 'X-Transaction': 'Load Photoitems'},
    			arguments : { 'X-Transaction': 'Load Photoitems'}
    		};
	var sUrl = EXPONENT.PATH_RELATIVE+"index.php?controller=photo&action=showall&view=photolist&ajax_action=1&src={/literal}{$__loc->src}{literal}";

	var handleSuccess = function(ioId, o){
        if(o.responseText){
                photolist.setContent(o.responseText);
                photolist.all('script').each(function(n){
                if(!n.get('src')){
                    eval(n.get('innerHTML'));
                } else {
                    Y.Get.script(n.get('src'));
                };
            });
            photolist.all('link').each(function(n){
                Y.Get.css(n.get('href'));
            });
        } else {
            photolist.one('.loadingdiv').remove();
        }
	};

	//A function handler to use for failed requests:
	var handleFailure = function(ioId, o){
		Y.log("The failure handler was called.  Id: " + ioId + ".", "info", "photoitems nav");
	};

	//Subscribe our handlers to IO's global custom events:
	Y.on('io:success', handleSuccess);
	Y.on('io:failure', handleFailure);

    photolist.delegate('click', function(e){
        e.halt();
        History.pushState({name:'{/literal}{$name}{literal}',rel:e.currentTarget.get('rel')}, '{/literal}{'Photos'|gettext}{literal}', orig_url+page_parm+e.currentTarget.get('rel'));
        cfg.data = "page="+e.currentTarget.get('rel');
        var request = Y.io(sUrl, cfg);
        photolist.setContent(Y.Node.create('{/literal}{loading title="Loading Photos"|gettext}{literal}'));
    }, 'a.pager');

    // Watches the browser history for changes
    window.addEventListener('popstate', function(e) {
        state = History.getState()
        if (state.data.name == '{/literal}{$name}{literal}') {
            // moving to a new page
            cfg.data = "page="+state.data.rel;
            var request = Y.io(sUrl, cfg);
            photolist.setContent(Y.Node.create('{/literal}{loading title="Loading Photos"|gettext}{literal}'));
        }
    });
});
{/literal}
{/script}
{/if}
