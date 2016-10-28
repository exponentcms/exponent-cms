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

{uniqueid prepend="mediaplayer" assign="name"}

{css unique="player" link="`$asset_path`css/player.css"}

{/css}
{css unique="mediaelement" link="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelementplayer.css"}

{/css}
{css unique="mediaelement-skins" link="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mejs-skins.css"}

{/css}

<div class="module flowplayer mediaplayer showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
	{permissions}
		<div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit rank=1 title="Add a Media piece at the Top"|gettext text="Add a Media piece"|gettext}
			{/if}
			{if $permissions.manage}
				{ddrerank items=$page->records model="media" label="Media Pieces"|gettext}
			{/if}
		</div>
	{/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    <div id="{$name}list" class="yui3-g">
        {exp_include file='medialist.tpl'}
    </div>
</div>

{$control = ''}
{if $config.control_play}{$control = "`$control`'playpause',"}{/if}
{if $config.control_stop}{$control = "`$control`'stop',"}{/if}
{if $config.control_scrubber}{{$control = "`$control`'progress',"}}{/if}
{if $config.control_time}{{$control = "`$control`'duration',"}}{/if}
{if $config.control_volume}{$control = "`$control`'volume',"}{/if}
{if $config.control_fullscreen}{{$control = "`$control`'fullscreen'"}}{/if}
{if $control == ''}{$control = "'playpause','progress','current','duration','tracks','volume','fullscreen'"}{/if}

{script unique="mediaelement-src" jquery="jquery.colorbox" src="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelement-and-player.min.js"}
{/script}

{script unique="mediaplayer-`$name`"}
{literal}
    $('audio,video').mediaelementplayer({
        success: function(player, node) {
            $('#' + node.id + '-mode').html('mode: ' + player.pluginType);
        },
        features: [{/literal}{$control}{literal}]
    });

    $(document).ready(function(){
        $('.openColorbox').click(function(e){
            e.preventDefault();
            var d = $(this);
            var $c = d.parent().find('div.video.media');
            var $t = d.parent().find('.media-title');
            $.colorbox({
                width: "auto",
                inline: true,
                href: $c,
                title: $t.html(),
                opacity: 0.5,
                open: true,
                onLoad: function(){
                    $c.fadeIn()
                },
                onCleanup: function(){
                    $c.hide()
                },
                close:'<i class="fa fa-close" aria-label="close modal"></i>',
                previous:'<i class="fa fa-chevron-left" aria-label="previous photo"></i>',
                next:'<i class="fa fa-chevron-right" aria-label="next photo"></i>',
            })
        });

        //$("#openColorbox").colorbox({
        //    inline:true,
        //    width:"auto",
        //    height:"auto",
        //    href:$(this).next(),
        //    onOpen: function() {
        //        $.colorbox.element().next().show();
        //    },
        //    onClosed: function() {
        //        $.colorbox.element().next().hide();
        //    }
        //});
    });
{/literal}
{/script}

{if $smarty.const.AJAX_PAGING}
{if empty($params.page)}
    {$params.page = 1}
{/if}
{script unique="`$name`listajax" yui3mods="node,io,node-event-delegate" jquery="jquery.history"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
    var medialist = Y.one('#{/literal}{$name}{literal}list');
    var page_parm = '';
    if (EXPONENT.SEF_URLS) {
        page_parm = '/page/';
    } else {
        page_parm = '&page=';
    }
    var History = window.History;
    History.pushState({name:'{/literal}{$name}{literal}',rel:'{/literal}{$params.page}{literal}'});
    {/literal}
        {$orig_params = ['controller' => 'media', 'action' => 'showall', 'src' => $params.src]}
    {literal}
    var orig_url = '{/literal}{makeLink($orig_params)}{literal}';
    var cfg = {
    			method: "POST",
    			headers: { 'X-Transaction': 'Load Mediaitems'},
    			arguments : { 'X-Transaction': 'Load Mediaitems'}
    		};
	var sUrl = EXPONENT.PATH_RELATIVE+"index.php?controller=media&action=showall&view=medialist&ajax_action=1&src={/literal}{$__loc->src}{literal}";

	var handleSuccess = function(ioId, o){
        if(o.responseText){
                medialist.setContent(o.responseText);
                medialist.all('script').each(function(n){
                if(!n.get('src')){
                    eval(n.get('innerHTML'));
                } else {
                    Y.Get.script(n.get('src'));
                };
            });
            medialist.all('link').each(function(n){
                Y.Get.css(n.get('href'));
            });
        } else {
            medialist.one('.loadingdiv').remove();
        }
	};

	//A function handler to use for failed requests:
	var handleFailure = function(ioId, o){
		Y.log("The failure handler was called.  Id: " + ioId + ".", "info", "mediaitems nav");
	};

	//Subscribe our handlers to IO's global custom events:
	Y.on('io:success', handleSuccess);
	Y.on('io:failure', handleFailure);

    medialist.delegate('click', function(e){
        e.halt();
        History.pushState({name:'{/literal}{$name}{literal}',rel:e.currentTarget.get('rel')}, '{/literal}{'Media'|gettext}{literal}', orig_url+page_parm+e.currentTarget.get('rel'));
        cfg.data = "page="+e.currentTarget.get('rel');
        var request = Y.io(sUrl, cfg);
          medialist.setContent(Y.Node.create('{/literal}{loading title="Loading Media"|gettext}{literal}'));
    }, 'a.pager');

    // Watches the browser history for changes
    window.addEventListener('popstate', function(e) {
        state = History.getState()
        if (state.data.name == '{/literal}{$name}{literal}') {
            // moving to a new page
            cfg.data = "page="+state.data.rel;
            var request = Y.io(sUrl, cfg);
              medialist.setContent(Y.Node.create('{/literal}{loading title="Loading Media"|gettext}{literal}'));
        }
    });
});
{/literal}
{/script}
{/if}
