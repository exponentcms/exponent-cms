{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

<div class="module filedownload show">
	<div class="item">
        {$filetype=$record->expFile.downloadable[0]->filename|regex_replace:"/^.*\.([^.]+)$/D":"$1"}
        {if $record->expFile.preview[0] != ""}
            {img class="preview-img" file_id=$record->expFile.preview[0]->id square=150}
        {/if}
        {if $record->title}<h2>{$record->title}</h2>{/if}
        {printer_friendly_link}{export_pdf_link prepend='&nbsp;&nbsp;|&nbsp;&nbsp;'}{br}
        {assign var=myloc value=serialize($__loc)}
        {permissions}
			<div class="item-actions">
				{if $permissions.edit == 1}
                    {if $myloc != $record->location_data}
                        {if $permissions.manage == 1}
                            {icon action=merge id=$record->id title="Merge Aggregated Content"|gettext}
                        {else}
                            {icon img='arrow_merge.png' title="Merged Content"|gettext}
                        {/if}
                    {/if}
					{icon action=edit record=$record title="Edit this file"|gettext}
				{/if}
				{if $permissions.delete == 1}
					{icon action=delete record=$record title="Delete this file"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this file?"|gettext)|cat:"');"}
				{/if}
                {if $permissions.manage == 1}
                    {icon controller=expTag action=manage text="Manage Tags"|gettext}
                {/if}
			</div>
        {/permissions}
        <div class="attribution">
            <p>
            <span class="label dated">{'Dated'|gettext}:</span>
            <span class="value">{$file->publish_date|format_date}</span>
            &nbsp;|&nbsp;
            {if $record->expFile.downloadable[0]->duration}
                <span class="label size">{'Duration'}:</span>
                <span class="value">{$record->expFile.downloadable[0]->duration}</span>
            {else}
                <span class="label size">{'File Size'}:</span>
                {if $record->expFile.downloadable[0]->filesize >= 1048576}
                    <span class="value">{$record->expFile.downloadable[0]->filesize|megabytes} {'mb'|gettext}</span>
                {elseif $record->expFile.downloadable[0]->filesize >= 1024}
                    <span class="value">{$record->expFile.downloadable[0]->filesize|kilobytes} {'kb'|gettext}</span>
                {else}
                    <span class="value">{$record->expFile.downloadable[0]->filesize} {'bytes'|gettext}</span>
                {/if}
            {/if}
            &nbsp;|&nbsp;
            <span class="label downloads"># {'Downloads'|gettext}:</span>
            <span class="value">{$record->downloads}</span>
            {if $record->expTag|@count>0 && !$config.disabletags}
                &nbsp;|&nbsp;
                <span class="tags">
                    {'Tags'|gettext}:
                    {foreach from=$record->expTag item=tag name=tags}
                        <a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title}</a>{if $smarty.foreach.tags.last != 1},{/if}
                    {/foreach}
                </span>
            {/if}
            </p>
        </div>
        <div class="bodycopy">
            {$record->body}
        </div>
        {icon action=downloadfile fileid=$record->id text='Download'|gettext}
        {if $config.show_player && ($filetype == "mp3" || $filetype == "flv" || $filetype == "f4v")}
            <a href="{$record->expFile.downloadable[0]->url}" style="display:block;width:360px;height:{if $filetype == "mp3"}26{else}240{/if}px;" class="filedownload-media"></a>
        {/if}
        {clear}
        {if $config.usescomments == true}
            {comments content_type="filedownload" content_id=$record->id title="Comments"|gettext}
        {/if}  
	</div>		
</div>

{if $config.show_player}
    {script unique="filedownload" src="`$smarty.const.PATH_RELATIVE`external/flowplayer3/flowplayer-3.2.9.min.js"}
    {literal}
    flowplayer("a.filedownload-media", EXPONENT.PATH_RELATIVE+"external/flowplayer3/flowplayer-3.2.10.swf",
        {
    		wmode: 'opaque',
    		clip: {
    			autoPlay: false,
    			},
            plugins:  {
                controls: {
                    play: true,
                    scrubber: true,
                    fullscreen: false,
                    autoHide: false
                }
            }
        }
    );
    {/literal}
    {/script}
{/if}
