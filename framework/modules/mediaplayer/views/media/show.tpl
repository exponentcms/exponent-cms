{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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
{css unique="mediaelement" link="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mediaelementplayer.min.css"}

{/css}
{css unique="mediaelement-skins" link="`$smarty.const.PATH_RELATIVE`external/mediaelement/build/mejs-skins.css"}

{/css}

<div class="module flowplayer mediaplayer showall">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    <div id="{$name}list" class="yui3-g">
        {$myloc=serialize($__loc)}
        <div class="yui3-u-1-3">
            {$filetype=$record->expFile.media[0]->filename|regex_replace:"/^.*\.([^.]+)$/D":"$1"}
            <div class="item">
                <{$config.item_level|default:'h2'} class="media-title">{$record->title}</{$config.item_level|default:'h2'}>
            {tags_assigned record=$record}
            {permissions}
                <div class="item-actions">
                    {if $permissions.edit || ($permissions.create && $record->poster == $user->id)}
                        {if $myloc != $record->location_data}
                            {if $permissions.manage}
                                {icon action=merge id=$record->id title="Merge Aggregated Content"|gettext}
                            {else}
                                {icon img='arrow_merge.png' title="Merged Content"|gettext}
                            {/if}
                        {/if}
                        {icon action=edit record=$record title="Edit"|gettext|cat:" "|cat:$record->title|cat:" "|cat:("media piece"|gettext)}
                    {/if}
                    {if $permissions.delete || ($permissions.create && $record->poster == $user->id)}
                        {icon action=delete record=$record title="Delete"|gettext|cat:" "|cat:$record->title|cat:" "|cat:("media piece"|gettext)}
                    {/if}
                </div>
            {/permissions}
            <div class="video media" style="width:80%;max-width:960px;margin:0 auto;">
                {if $filetype == "mp3"}
                    <audio class="{$config.video_style}" id="player{$record->expFile.media[0]->id}" controls="controls" preload="none"
                           src="{$smarty.const.PATH_RELATIVE}{$record->expFile.media[0]->directory}{$record->expFile.media[0]->filename}" type="audio/mp3"{if $config.autoplay} autoplay="true" {/if}>
                    </audio>
                {elseif $filetype == "mp4" || $filetype == "m4v" || $filetype == "webm" || $filetype == "ogv" || $filetype == "flv" || $filetype == "f4v" || $record->url != ""}
                    <video class="{$config.video_style}"  style="width:100%;height:100%;" width="{$record->width|default:$config.video_width}" height="{$record->height|default:$config.video_height}"
                           id="player{$record->expFile.media[0]->id}"
                        {if $config.autoplay}
                            autoplay
                        {/if}
                        {if $record->expFile.splash[0]->id}
                            poster="{$smarty.const.PATH_RELATIVE}{$record->expFile.splash[0]->directory}{$record->expFile.splash[0]->filename}"
                        {/if}
                           controls="controls" preload="none">
                        {if $record->media_type == "file"}
                            <source type="{$record->expFile.media[0]->mimetype}" src="{$smarty.const.PATH_RELATIVE}{$record->expFile.media[0]->directory}{$record->expFile.media[0]->filename}" />
                        {else}
                            <source type="video/youtube" src="{$record->url}" />
                        {/if}
                    </video>
                {/if}
            </div>
            <div class="bodycopy">
                {$record->body}
            </div>
        </div>
        {clear}
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
    mejs.i18n.language('{/literal}{substr($smarty.const.LOCALE,0,2)}{literal}'); // Setting language
    $('audio,video').mediaelementplayer({
        // Do not forget to put a final slash (/)
        pluginPath: '../build/',
        iconSprite: EXPONENT.PATH_RELATIVE+'external/mediaelement/build/mejs-controls.svg',
        // this will allow the CDN to use Flash without restrictions
        // (by default, this is set as `sameDomain`)
        shimScriptAccess: 'always',
        success: function(player, node) {
        // $('#' + node.id + '-mode').html('mode: ' + player.rendererName);
        },
        features: [{/literal}{$control}{literal}]
    });
{/literal}
{/script}
