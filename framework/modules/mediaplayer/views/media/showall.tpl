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

{uniqueid prepend="mediaplayer" assign="name"}

{css unique="mediaelement" link="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelementplayer.css"}

{/css}
{css unique="mediaelement-skins" link="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mejs-skins.css"}

{/css}

<div class="module flowplayer mediaplayer showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h1>{$moduletitle}</h1>{/if}
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
    <div id="{$name}list">
        {include 'medialist.tpl'}
    </div>
</div>

{$control = ''}
{if $config.control_play}{$control = "`$control`'playpause',"}{/if}
{if $config.control_stop}{$control = "`$control`'stop',"}{/if}
{if $config.control_scrubber}{{$control = "`$control`'progress',"}}{/if}
{if $config.control_time}{{$control = "`$control`'duration',"}}{/if}
{if $config.control_volume}{$control = "`$control`'volume',"}{/if}
{if $config.control_fullscreen}{{$control = "`$control`'fullscreen'"}}{/if}

{script unique="mediaelement-src" jquery="1" src="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelement-and-player.min.js"}
{/script}

{script unique="mediaplayer-`$name`"}
{literal}
    $('audio,video').mediaelementplayer({
        success: function(player, node) {
            $('#' + node.id + '-mode').html('mode: ' + player.pluginType);
        },
        features: [{/literal}{$control}{literal}]
    });
{/literal}
{/script}

{if $config.ajax_paging}
{script unique="`$name`listajax" yui3mods="1"}
{literal}
YUI(EXPONENT.YUI3_CONFIG).use('node','io','node-event-delegate', function(Y) {
    var medialist = Y.one('#{/literal}{$name}{literal}list');
    var cfg = {
    			method: "POST",
    			headers: { 'X-Transaction': 'Load Mediaitems'},
    			arguments : { 'X-Transaction': 'Load Mediaitems'}
    		};

    src = '{/literal}{$__loc->src}{literal}';
	var sUrl = EXPONENT.PATH_RELATIVE+"index.php?controller=media&action=showall&view=medialist&ajax_action=1&src="+src;

	var handleSuccess = function(ioId, o){
//		Y.log(o.responseText);
		Y.log("The success handler was called.  Id: " + ioId + ".", "info", "mediaitems nav");

        if(o.responseText){
                medialist.setContent(o.responseText);
                medialist.all('script').each(function(n){
                if(!n.get('src')){
                    eval(n.get('innerHTML'));
                } else {
                    var url = n.get('src');
                    if (url.indexOf("ckeditor")) {
                        Y.Get.script(url);
                    };
                };
            });
            medialist.all('link').each(function(n){
                var url = n.get('href');
                Y.Get.css(url);
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
        cfg.data = "page="+e.currentTarget.get('rel');
        var request = Y.io(sUrl, cfg);
        medialist.setContent(Y.Node.create('<div class="loadingdiv">{/literal}{"Loading Media"|gettext}{literal}</div>'));
    }, 'a.pager');
});
{/literal}
{/script}
{/if}
