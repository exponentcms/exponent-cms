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

<div class="module photoalbum slideshow">
    <div id="ss-{$name}" class="slideshow-container">
        <ul>
            <div class="slideshow-frame-ex">
            {$quality=$config.quality|default:$smarty.const.THUMB_QUALITY}
            {foreach key=key from=$slides item=slide name=slides}
                <li class="slide"{if !$config.hidetext} data-plugin-slide-caption='{$slide->title|escape} {$slide->body|escape}'{/if}>
                    {img file_id=$slide->id w=1000 aoe=1 class="slide-image" far=TL f=jpeg q=$quality|default:75}
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
{script unique="ssj-`$name`" jquery="jquery.excoloSlider"}
{literal}
    $(function () {
        $("#ss-{/literal}{$name}{literal} .slideshow-frame-ex").excoloSlider({
            width : {/literal}{$config.width|default:350}{literal},
            height : {/literal}{$config.height|default:300}{literal},
            interval : {/literal}{$config.speed|default:5}000{literal},
            captionAutoHide : true,
            autoPlay : {/literal}{$config.autoplay|default:true}{literal},
            animationDuration : {/literal}{$config.duration|default:5}00{literal},
//            prevButtonImage : EXPONENT.JQUERY_RELATIVE +"addons/images/prev.png",
//            nextButtonImage : EXPONENT.JQUERY_RELATIVE +"addons/images/next.png",
//            pagerImage : EXPONENT.JQUERY_RELATIVE +"addons/images/pagericon.png",
        });
    });
{/literal}
{/script}
{/if}
