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

    {pagelinks paginate=$page top=1}
    {$myloc=serialize($__loc)}
    {foreach from=$page->records item=item key=key}
        <div class="col-sm-{if $config.use_lightbox}4{else}12{/if}">
            {$filetype=$item->expFile.media[0]->filename|regex_replace:"/^.*\.([^.]+)$/D":"$1"}
            <div class="item">
                <{$config.item_level|default:'h2'} class="media-title">{$item->title}</{$config.item_level|default:'h2'}>
                {tags_assigned record=$item}
                {permissions}
                    <div class="item-actions">
                        {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                            {if $myloc != $item->location_data}
                                {if $permissions.manage}
                                    {icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                {else}
                                    {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                {/if}
                            {/if}
                            {icon action=edit record=$item title="Edit"|gettext|cat:" "|cat:$item->title|cat:" "|cat:("media piece"|gettext)}
                        {/if}
                        {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                            {icon action=delete record=$item title="Delete"|gettext|cat:" "|cat:$item->title|cat:" "|cat:("media piece"|gettext)}
                        {/if}
                    </div>
                {/permissions}
                {if $config.use_lightbox}
                    {img file_id=$item->expFile.splash[0]->id class="openColorbox" h=$config.thumb_width|default:"64" w=$config.thumb_height|default:"64" title='Click to view video'|gettext}
                {/if}
                <div class="video media"{if $config.use_lightbox} style='display:none'{/if}>
                    {if $filetype == "mp3"}
                        <audio class="{$config.video_style}" id="{$item->expFile.media[0]->filename}" controls="controls" preload="none"
                            src="{$smarty.const.PATH_RELATIVE}{$item->expFile.media[0]->directory}{$item->expFile.media[0]->filename}" type="audio/mp3"{if $config.autoplay} autoplay="true" {/if}>
                        </audio>
                    {elseif $filetype == "mp4" || $filetype == "m4v" || $filetype == "webm" || $filetype == "ogv" || $filetype == "flv" || $filetype == "f4v" || $item->url != ""}
                        <video class="{$config.video_style}" width="{$item->width|default:$config.video_width}" height="{$item->height|default:$config.video_height}"
                            id="player{$item->expFile.media[0]->id}"
                            {if $config.autoplay}
                                autoplay="true"
                            {/if}
                            {if $item->expFile.splash[0]->id}
                                poster="{$smarty.const.PATH_RELATIVE}{$item->expFile.splash[0]->directory}{$item->expFile.splash[0]->filename}"
                            {/if}
                            controls="controls" preload="none">
                            {if $item->media_type == "file"}
                                <source type="{$item->expFile.media[0]->mimetype}" src="{$smarty.const.PATH_RELATIVE}{$item->expFile.media[0]->directory}{$item->expFile.media[0]->filename}" />
                            {else}
                                <source type="video/youtube" src="{$item->url}" />
                            {/if}
                        </video>
                    {/if}
                </div>
                <div class="bodycopy">
                    {$item->body}
                </div>
            </div>
            {permissions}
                <div class="module-actions">
                    {if $permissions.create}
                        {icon class=add action=edit rank=$item->rank+1 title="Add a Media piece Here"|gettext text="Add a Media piece"|gettext}
                    {/if}
                </div>
            {/permissions}
            {clear}
        </div>
    {/foreach}
    {pagelinks paginate=$page bottom=1}
