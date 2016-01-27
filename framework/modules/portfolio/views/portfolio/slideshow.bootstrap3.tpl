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

{css unique="carousel-`$name`" lesscss="`$asset_path`less/carousel.less" corecss="animate"}
{literal}
    .{/literal}{$name}{literal} .cu-controls .carousel-indicators li.middle {
    	width: {/literal}{100/$slides|@count}{literal}%;
    }
{/literal}
{/css}

<div class="row module portfolio {$name}">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-12">
        {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
        {permissions}
            <div class="module-actions">
                {if $permissions.create}
                    {icon class=add action=edit rank=1 text="Add a Portfolio Slide"|gettext}
                {/if}
                {if $permissions.manage}
                    {if !$config.disabletags}
                        {icon controller=expTag class="manage" action=manage_module model='portfolio' text="Manage Tags"|gettext}
                    {/if}
                    {if $config.usecategories}
                        {icon controller=expCat action=manage model='portfolio' text="Manage Categories"|gettext}
                    {/if}
                    {*{if $slides|@count>1 && $rank == 1}*}
                    {if $slides|@count>1 && $config.order == 'rank'}
                        {ddrerank items=$slides model="portfolio" label="Portfolio Pieces"|gettext}
                    {/if}
                {/if}
            </div>
        {/permissions}
        {if $config.moduledescription != ""}
            {$config.moduledescription}
        {/if}
            </div>
        </div>
    </div>
    {$myloc=serialize($__loc)}

    <!-- begin Carousel -->
    <div class="row">
        <div id="ss-{$name}" class="col-sm-12 carousel slide{if $config.anim_in == 'fade'} carousel-fade{/if}">
        {if !$config.hidecontrols && $slides|@count > 1}
            <!-- begin Controls -->
            <div class="row cu-controls">
                <div class="col-sm-12">
                    <a href="#ss-{$name}" data-slide="prev" class="sercacontrol"><i class="glyphicon glyphicon-chevron-left"></i></a>
                    <a href="#ss-{$name}" data-slide="next" class="sercacontrol next"><i class="glyphicon glyphicon-chevron-right"></i></a>
                    <ol class="carousel-indicators">
                        {foreach key=key from=$slides item=slide name=slides}
                            <li data-target="#ss-{$name}" class="middle{if $smarty.foreach.slides.first} active{/if}" data-slide-to="{$smarty.foreach.slides.iteration - 1}">{if !empty($slide->expCat[0]->title)}{$slide->expCat[0]->title}{else}{$slide->title}{/if}</li>
                        {/foreach}
                    </ol>
                </div>
            </div>
            <!-- end Controls -->
        {/if}
            <div class="carousel-inner">
                {foreach key=key from=$slides item=slide name=slides}
                    <!-- begin Item -->
                    <div class="item{if $smarty.foreach.slides.first} active{/if}">
                        <div class="{if (!empty($slide->expFile[0]->id))}col-sm-7{else}col-sm-12{/if}">
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
                                        {icon action=copy record=$slide title="Copy"|gettext|cat:" `$item->title`"}
                                    {/if}
                                    {if $permissions.delete || ($permissions.create && $slide->poster == $user->id)}
                                        {icon action=delete record=$slide title="Delete"|gettext|cat:" `$item->title`"}
                                    {/if}
                                    {if $permissions.create}
                                        {icon class=add action=edit rank=$slide->rank+1 title="Add another slide here"|gettext  text="Add another slide here"|gettext}
                                    {/if}
                                </div>
                            {/permissions}
                            {if !$config.hidetext}
                                <div class="bodycopy">
                                    <{$config.item_level|default:'h2'}>
                                        <a href="{link action="show" title=$slide->sef_url}" title="{$slide->body|summarize:"html":"para"}">
                                            {$slide->expCat[0]->title} {$slide->title}
                                        </a>
                                    </{$config.item_level|default:'h2'}>
                                    {$slide->body}
                                </div>
                            {/if}
                        </div>
                        <div class="{if (!empty($slide->expFile[0]->id))}col-sm-4 col-md-offset-1{else}hidden{/if}">
                            {if $config.quality==100}
                                <img src="{$slide->expFile[0]->url}" class="img-responsive slide-image" />
                            {else}
                                {img file_id=$slide->expFile[0]->id w=$config.width|default:350 h=$config.height|default:200 class="img-responsive slide-image" zc=1 q=$config.quality|default:80}
                            {/if}
                        </div>
                    </div>
                    <!-- end Item -->
                {foreachelse}
                    {permissions}
                        {if $permissions.create}
                            {message class=notice text="No slides created yet"|gettext}
                        {/if}
                    {/permissions}
                {/foreach}
            </div>
        {if $config.hidecontrols && $slides|@count > 1}
            <!-- begin Controls -->
            <div class="row cu-controls">
                <div class="col-sm-12">
                    <a href="#ss-{$name}" data-slide="prev" class="sercacontrol"><i class="glyphicon glyphicon-chevron-left"></i></a>
                    <a href="#ss-{$name}" data-slide="next" class="sercacontrol next"><i class="glyphicon glyphicon-chevron-right"></i></a>
                    <ol class="carousel-indicators">
                        {foreach key=key from=$slides item=slide name=slides}
                            <li data-target="#ss-{$name}" class="middle{if $smarty.foreach.slides.first} active{/if}" data-slide-to="{$smarty.foreach.slides.iteration - 1}">{if !empty($slide->expCat[0]->title)}{$slide->expCat[0]->title}{else}{$slide->title}{/if}</li>
                        {/foreach}
                    </ol>
                </div>
            </div>
            <!-- end Controls -->
        {/if}
        </div>
    </div>
    <!-- end Carousel -->
</div>

{if $slides|@count > 1}
{script unique="ssc-`$name`" bootstrap="carousel,transition" jquery="bootstrap-touch-carousel"}
{if $config.speed}
{literal}
    $('#ss-{/literal}{$name}{literal}').carousel({
        interval: {/literal}{$config.speed}{literal}000
    });
    Hammer.defaults.behavior.touchAction = 'pan-y';
{/literal}
{/if}
{/script}
{/if}
