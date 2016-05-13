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
 
{uniqueid prepend="slideshow`$params.record->id`" assign="name"}

{css unique="files-gallery" corecss="common" link="`$smarty.const.PATH_RELATIVE`framework/modules/common/assets/css/filedisplayer.css"}

{/css}
{css unique="slideshow-bs" link="`$smarty.const.PATH_RELATIVE`framework/modules/photoalbum/assets/css/slideshow_bs.css"}

{/css}
{css unique="owlcarousel-theme" link="`$smarty.const.PATH_RELATIVE`external/jquery/addons/css/owl.theme.default.css" corecss="animate"}

{/css}

<div id="ss-{$name}" class="slideshow-container">
    <ul>
        <div class="slideshow-frame-ex owl-carousel owl-theme">
            {$quality=$config.quality|default:$smarty.const.THUMB_QUALITY}
            {if !$quality}
                {$quality=THUMB_QUALITY}
            {/if}
            {foreach key=key from=$files item=slide name=slides}
                {if $slide->title != ""}
                    {$title = $slide->title}
                {else}
                    {$title = $slide->filename}
                {/if}
                <li class="slide">
                    {if $config.quality==100}
                        <img src="{$slide->url}" class="slide-image" title="{$title}" />
                    {else}
                        {img title="{$title}" file_id=$slide->id w=$config.width|default:350 h=$config.height|default:200 class="slide-image" far=TL f=jpeg q=$quality|default:75}
                    {/if}
                    {if !$config.hidetext}
                        <div class="bodycopy">
                            <h4>{$title}</h4>
                        <div>
                    {/if}
                </li>
            {/foreach}
        </div>
    </ul>
</div>

{if $files|@count > 1}
{script unique="ssj-`$name`" jquery="owl.carousel"}
{literal}
    $(function () {
        $("#ss-{/literal}{$name}{literal} .owl-carousel").owlCarousel({
//            width : {/literal}{$config.width|default:350}{literal},
//            height : {/literal}{$config.height|default:300}{literal},
            items: 1,
            loop: true,
//            autoWidth: true,
            nav : {/literal}{!$config.hidecontrols}{literal},
            navText : ['{/literal}{expTheme::iconStyle('chevron-left',' ')}{literal}','{/literal}{expTheme::iconStyle('chevron-right',' ')}{literal}'],
            autoplay : {/literal}{if $config.autoplay==1}true{else}false{/if}{literal},
            autoplayTimeout : {/literal}{$config.speed|default:5}000{literal},
            autoplayHoverPause : true,
//            captionAutoHide : true,
            animateIn: '{/literal}{$config.anim_in|default:'fadeIn'}{literal}',
            animateOut: '{/literal}{$config.anim_out|default:'fadeOut'}{literal}',
//            animationDuration : {/literal}{$config.duration|default:5}00{literal},
        });
    });
{/literal}
{/script}
{/if}
