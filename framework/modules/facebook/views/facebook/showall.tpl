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

{uniqueid prepend="fb" assign="name"}

{if $config.resp_width}
{css unique=fblike}
    .fb-like, .fb-like span, .fb-like span iframe[style] {
        width: 100% !important;
    }
{/css}
{/if}

<div class="module facebook show">
    <div id="fb-root"></div>
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<div class=fb-title'><strong>{$moduletitle}</strong></div>{/if}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
    {*{else}*}
        {*{br}*}
   	{/if}
    <div id="fb-container-{$name}" class="fb-like" data-href="{$facebook_url}" data-send="false" data-width="{$config.width|default:'450'}" data-show-faces="{if $config.showfaces}true{else}false{/if}" data-font="{$config.font|default:''}" data-colorscheme="{$config.color_scheme|default:''}" data-action="{$config.verb|default:''}"></div>
</div>

{script unique='facebook_src'}
{literal}
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";  //FIXME add &appId=ADD YOUR APP ID HERE ???
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
{/literal}
{if $config.resp_width}
{literal}
    $(window).on("load resize", function(){
        $('#fb-container-{/literal}{$name}{literal}').attr('data-width', $('#fb-container-{/literal}{$name}{literal}').parent().width());
        FB.XFBML.parse();
    });
{/literal}
{/if}
{/script}
