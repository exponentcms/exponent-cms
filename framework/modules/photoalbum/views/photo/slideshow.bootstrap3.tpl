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

{uniqueid prepend="slideshow" assign="name"}

{css unique="slideshow-bs" link="`$asset_path`css/slideshow_bs.css"}

{/css}
{css unique="owlcarousel-theme" link="`$smarty.const.PATH_RELATIVE`external/jquery/addons/css/owl.theme.default.css" corecss="animate"}

{/css}

<div class="module photoalbum slideshow">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {permissions}
    <div class="module-actions">
        {if $permissions.create}
            {icon class=add action=edit rank=1 text="Add a Slide"|gettext}
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
            {if $slides|@count>1 && $config.order == 'rank'}
                {ddrerank items=$slides model="photo" label="Slides"|gettext}
            {/if}
        {/if}
    </div>
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {$myloc=serialize($__loc)}
    <div id="ss-{$name}" class="slideshow-container">
        <ul>
            <div class="slideshow-frame-ex owl-carousel owl-theme">
            {$quality=$config.quality|default:$smarty.const.THUMB_QUALITY}
            {foreach key=key from=$slides item=slide name=slides}
                <li class="slide"{if !$config.hidetext} data-plugin-slide-caption='<{$config.item_level|default:'h2'}>{$slide->title|escape}</{$config.item_level|default:'h2'}> {$slide->body|escape}'{/if}>
                    {permissions}
                        <div class="item-actions">
                            {if $permissions.edit || ($permissions.create && $slide->poster == $user->id)}
                                {if $myloc != $slide->location_data}
                                    {if $permissions.manage}
                                        {icon action=merge id=$slide->id title="Merge Aggregated Content"|gettext}
                                    {else}
                                        {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                    {/if}
                                {/if}
                                {icon action=edit record=$slide title="Edit"|gettext|cat:" `$item->title`"}
                            {/if}
                            {if $permissions.delete || ($permissions.create && $slide->poster == $user->id)}
                                {icon action=delete record=$slide title="Delete"|gettext|cat:" `$item->title`"}
                            {/if}
                            {if $permissions.create}
                                {icon class=add action=edit rank=$slide->rank+1 title="Add another slide here"|gettext  text="Add After"|gettext}
                            {/if}
                        </div>
                    {/permissions}
                    {if $slide->link}
                        <a href="{$slide->link}">
                    {/if}
                    {*{if $config.quality==100}*}
                        {*<img src="{$slide->expFile[0]->url}" class="slide-image" />*}
                    {*{else}*}
                        {img file_id=$slide->expFile[0]->id w=1000 aoe=1 class="slide-image" far=TL f=jpeg q=$quality|default:75 alt=$slide->alt}
                    {*{/if}*}
                    {if $slide->link}
                        </a>
                    {/if}
                </li>
            {foreachelse}
                {permissions}
                    {if $permissions.create}
                        {message class=notice text="No slides created yet"|gettext}
                    {/if}
                {/permissions}
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
//            prevButtonImage : EXPONENT.JQUERY_RELATIVE +"addons/images/prev.png",
//            nextButtonImage : EXPONENT.JQUERY_RELATIVE +"addons/images/next.png",
//            pagerImage : EXPONENT.JQUERY_RELATIVE +"addons/images/pagericon.png",
        });
    });
{/literal}
{/script}
{/if}
