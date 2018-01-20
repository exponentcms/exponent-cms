{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
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

{uniqueid prepend="slideshow" assign="name"}

{css unique="slideshow-bs" link="`$smarty.const.PATH_RELATIVE`framework/modules/photoalbum/assets/css/slideshow_bs.css"}

{/css}
{css unique="owlcarousel-theme" link="`$smarty.const.PATH_RELATIVE`external/jquery/addons/css/owl.theme.default.css" corecss="animate"}

{/css}

<div class="module photoalbum slideshow">
    <div id="ss-{$name}" class="slideshow-container">
        <ul>
            <div class="slideshow-frame-ex{if $slides|@count > 1} owl-carousel owl-theme{/if}">
            {$quality=$config.quality|default:$smarty.const.THUMB_QUALITY}
            {foreach key=key from=$slides item=slide name=slides}
                <li class="slide"{if !$config.hidetext} data-plugin-slide-caption='{$slide->title|escape} {$slide->body|escape}'{/if}>
                    {img file_id=$slide->id w=1000 aoe=1 class="slide-image" far=TL f=jpeg q=$quality|default:75 alt=$slide->alt|default:'Sermon Slide'|gettext}
                </li>
            {foreachelse}
                <li>
                    {permissions}
                        {if $permissions.create}
                            {message class=notice text="No slides created yet"|gettext}
                        {/if}
                    {/permissions}
                </li>
            {/foreach}
            </div>
        </ul>
    </div>
</div>

{if $slides|@count > 1}
{script unique="ssj-`$name`" jquery="owl.carousel"}
{literal}
    $(function () {
        $("#ss-{/literal}{$name}{literal} .owl-carousel").owlCarousel({
//            width : {/literal}{$config.width|default:350}{literal},
//            height : {/literal}{$config.height|default:300}{literal},
            items: 1,
            loop: true,
//            autoWidth: true,
            nav : true,
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
