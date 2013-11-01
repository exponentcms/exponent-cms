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
 
{uniqueid prepend="slideshow`$params.record->id`" assign="name"}

{css unique="files-gallery" corecss="common" link="`$smarty.const.PATH_RELATIVE`framework/modules/common/assets/css/filedisplayer.css"}

{/css}

<div id="ss-{$name}" class="slideshow slideshow-container" style="width:{$config.width|default:350}px;">
    <ul class="slideshow-frame"{if $config.width} style="width:{$config.width}px;height:{$config.height}px;"{/if}>
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
            <li class="slide" style="position:absolute;{if $smarty.foreach.slides.first}z-index:4;{else}z-index:1;{/if}">
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
    </ul>
    {if !$config.hidecontrols}
    <div class="slideshow-buttons{if $config.dimcontrols} buttons-dim{/if}">
        <a id="prev{$name}" href="javascript:void(0);" class="prev_slide" title="Previous Slide"|gettext>
            &lt;&lt; {'Previous'|gettext}
        </a>
        <span class="slideshow-pagination">
            {foreach key=key from=$files item=slide name=slides}
            <a class="slide-page-link" href="#" rel="{$key}">
                {$smarty.foreach.slides.iteration}
            </a>
            {/foreach}
        </span>
        <a id="plps{$name}" href="javascript:void(0);" class="pause_slide" title="Pause Slideshow"|gettext>
            {'Pause'|gettext}
        </a>
        <a id="plps{$name}" href="javascript:void(0);" class="play_slide hide" title="Play Slideshow"|gettext>
            {'Play'|gettext}
        </a>
        <a id="next{$name}" href="javascript:void(0);" class="next_slide" title="Next Slide"|gettext>
            {'Next'|gettext} &gt;&gt;
        </a>
    </div>
    {/if}
</div>

{if $files|@count > 1}
{script unique="slideshow`$name`" yui3mods="anim"}
{literal}
EXPONENT.YUI3_CONFIG.modules = {
    'gallery-yui-slideshow': {
        fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/photoalbum/assets/js/yui3-slideshow.js',
        requires: ['anim','node','slideshow-css'],
    },
    'slideshow-css': {
        fullpath: EXPONENT.PATH_RELATIVE+'framework/modules/photoalbum/assets/css/yui3-slideshow.css',
        type: 'css'
    }
}

YUI(EXPONENT.YUI3_CONFIG).use('gallery-yui-slideshow', function(Y) {
    var oSlideshow = new Y.Slideshow('#ss-{/literal}{$name}{literal} .slideshow-frame',
    {
        interval:{/literal}{$config.speed|default:5}000{literal},
//        autoplay:{/literal}{$config.autoplay|default:true}{literal},
        ti:'{/literal}{$config.anim_in|default:"fadeIn"}{literal}',
        to:'{/literal}{$config.anim_out|default:"fadeOut"}{literal}',
        duration:{/literal}{$config.duration|default:0.5}{literal},
        nextButton:"#ss-{/literal}{$name}{literal} .next_slide",
        previousButton:"#ss-{/literal}{$name}{literal} .prev_slide",
        playButton:"#ss-{/literal}{$name}{literal} .play_slide",
        pauseButton:"#ss-{/literal}{$name}{literal} .pause_slide",
        pagination:"#ss-{/literal}{$name}{literal} .slideshow-pagination a"
    });
});

{/literal}
{/script}
{/if}
