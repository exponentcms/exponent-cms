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

<div class="module filedownload show">
	<div class="item">
        {if $record->expFile.preview[0] != ""}
            {img class="preview-img" file_id=$record->expFile.preview[0]->id square=150}
        {/if}
        {if $config.datetag}
            <p class="post-date">
                <span class="month">{$record->publish_date|format_date:"%b"}</span>
                <span class="day">{$record->publish_date|format_date:"%e"}</span>
                <span class="year">{$record->publish_date|format_date:"%Y"}</span>
            </p>
        {/if}
        {if $record->title}<h2>{$record->title}</h2>{/if}
        {printer_friendly_link}{export_pdf_link prepend='&#160;&#160;|&#160;&#160;'}{br}
        {subscribe_link}
        {$myloc=serialize($__loc)}
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
			</div>
        {/permissions}
        <div class="attribution">
            {if !$config.datetag}
                <span class="label dated">{'Dated'|gettext}:</span>
                <span class="value">{$file->publish_date|format_date}</span>
                &#160;|&#160;
            {/if}
            <span class="label downloads"># {'Downloads'|gettext}:</span>
            <span class="value">{$record->downloads}</span>
            {comments_count record=$record show=1 prepend='&#160;&#160;|&#160;&#160;'}
            {tags_assigned record=$record prepend='&#160;&#160;|&#160;&#160;'}
        </div>
        <div class="bodycopy">
            {$record->body}
        </div>
        {if $record->ext_file}
            <a class=downloadfile href="{$record->ext_file}" title="{'Download'|gettext}" target="_blank">{'Download'|gettext}</a>
        {else}
            {if count($record->expFile.downloadable) > 1}
                <h3 style="border-bottom:1px solid #777777;">{'Downloads'|gettext}</h3>
            {/if}
            <ul>
                {foreach from=$record->expFile.downloadable item=file key=filenum name=files}
                    <li style="margin-bottom: 10px;">
                        {$filetype=$file->filename|regex_replace:"/^.*\.([^.]+)$/D":"$1"}
                        {if $filenum == 0 && !empty($record->title)}
                            {$title = $record->title}
                        {elseif !empty($file->title)}
                            {$title = $file->title}
                        {else}
                            {$title = $file->filename}
                        {/if}
                        {icon action=downloadfile fileid=$record->id filenum=$filenum title='Download'|gettext text=$title}
                        <div class="attribution">
                            {if $record->expFile.downloadable[$filenum]->duration}
                                <span class="label size">{'Duration'}:</span>
                                <span class="value">{$record->expFile.downloadable[$filenum]->duration}</span>
                                &#160;|&#160;
                            {elseif $record->expFile.downloadable[$filenum]->filesize}
                                <span class="label size">{'File Size'}:</span>
                                <span class="value">{$record->expFile.downloadable[$filenum]->filesize|bytes}</span>
                            {/if}
                        </div>
                        {if $config.show_player && ($filetype == "mp3" || $filetype == "flv" || $filetype == "f4v")}
                            <a href="{$record->expFile.downloadable[$filenum]->url}" style="display:block;width:360px;height:{if $filetype == "mp3"}26{else}240{/if}px;" class="filedownload-media">
                                {if $record->expFile.preview[0] != ""}
                                    {img class="preview-img" file_id=$record->expFile.preview[0]->id w=360 h=240 zc=1}
                                {/if}
                            </a>
                        {/if}
                    </li>
                {/foreach}
            </ul>
        {/if}
        {clear}
        {comments record=$record title="Comments"|gettext}
	</div>
</div>

{if $config.show_player}
    {script unique="flowplayer" src="`$smarty.const.FLOWPLAYER_RELATIVE`flowplayer-`$smarty.const.FLOWPLAYER_MIN_VERSION`.min.js"}
    {/script}

    {script unique="filedownload"}
    {literal}
    flowplayer("a.filedownload-media", EXPONENT.FLOWPLAYER_RELATIVE+"flowplayer-"+EXPONENT.FLOWPLAYER_VERSION+".swf",
        {
    		wmode: 'transparent',
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
