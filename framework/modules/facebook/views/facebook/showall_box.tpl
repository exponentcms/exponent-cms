{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

{uniqueid prepend="fb" assign="name"}

{if $config.resp_width}
{css unique=fblikebox}
    .fb-like-box, .fb-like-box span, .fb-like-box span iframe[style] {
        width: 100% !important;
    }
{/css}
{/if}

<div class="module facebook showall">
    <div id="fb-root"></div>
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    <div id="fb-container-{$name}" class="fb-like-box" data-href="{$facebook_url}" data-width="{$config.width|default:'292'}"{if $config.height} data-height="{$config.height}"{/if} data-show-faces="{if $config.show_faces}true{else}false{/if}"{if $config.color_scheme} data-colorscheme="{$config.color_scheme}"{/if} data-stream="{if $config.stream}true{else}false{/if}" data-show-border="{if $config.show_border}true{else}false{/if}" data-header="{if $config.show_header}true{else}false{/if}"></div>
</div>

{script unique='facebook_src-`$name`'}
{literal}
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";  //FIXME add &appId=ADD YOUR APP ID HERE ???
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

//    $(window).bind("load resize", function(){
//        var container_width = $('#fb-container-{/literal}{$name}{literal}').width();
//        $('#fb-container-{/literal}{$name}{literal}').html('<div class="fb-like-box" ' +
//            'data-href="{/literal}{$facebook_url}{literal}" ' +
//            'data-width="' + container_width + '" data-height="{/literal}{$config.height}{literal}" data-show-faces="{/literal}{if $config.show_faces}true{else}false{/if}{literal}" ' +
//            'data-colorscheme="{/literal}{$config.color_scheme}{literal}"{/if} data-show-border="{/literal}{if $config.show_border}true{else}false{/if}{literal}" ' +
//            'data-stream="{/literal}{if $config.stream}true{else}false{/if}{literal}" data-header="{/literal}{if $config.show_header}true{else}false{/if}{literal}"></div>');
//        FB.XFBML.parse( );
//    });
{/literal}
{if $config.resp_width}
{literal}
    $(window).bind("load resize", function(){
        $('#fb-container-{/literal}{$name}{literal}').attr('data-width', $('#fb-container-{/literal}{$name}{literal}').parent().width());
        FB.XFBML.parse();
    });
{/literal}
{/if}
{/script}
