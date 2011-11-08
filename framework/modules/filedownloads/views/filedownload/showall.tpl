{*
 * Copyright (c) 2007-2011 OIC Group, Inc.
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

<div class="module filedownload showall">
    {if $config.enable_rss}
        <a class="rsslink" href="{podcastlink}">{'Subscribe to'|gettext} {$config.feed_title}</a>
    {/if}
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if} 
    {permissions}
        <div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit rank=1 title="Add a File at the Top"|gettext text="Add a File"|gettext}
			{/if}
			{if ($permissions.manage == 1 && $rank == 1)}
				{ddrerank items=$page->records model="filedownload" label="Downloadable Items"|gettext}
			{/if}
        </div>
    {/permissions}    
    {pagelinks paginate=$page top=1}
    {foreach from=$page->records item=file name=files}
        {$filetype=$file->expFile.downloadable[0]->filename|regex_replace:"/^.*\.([^.]+)$/D":"$1"}
			{if $file->expFile.preview[0] != "" && $config.show_icon}
				{img class="preview-img" file_id=$file->expFile.preview[0]->id square=150}
			{/if}
			{if $config.quick_download}
				<h2><a class="download" href="{link action=downloadfile fileid=$file->id}">{$file->title}</a></h2>
			{else}
				{if $file->title}<h2><a class="readmore" href="{link action=show title=$file->sef_url}">{$file->title}</a></h2>{/if}
			{/if}
			{if $config.show_info}
				<span class="label size">{'File Size'}:</span>
				<span class="value">{$file->expFile.downloadable[0]->filesize|kilobytes}{'Kb'|gettext}</span>
				&nbsp;&nbsp;
				<span class="label downloads"># {'Downloads'|gettext}:</span>
				<span class="value">{$file->downloads}</span>
			{/if}
			{permissions}
				<div class="item-actions">
					{if $permissions.edit == 1}
						{icon action=edit record=$file title="Edit this file"|gettext}
					{/if}
					{if $permissions.delete == 1}
						{icon action=delete record=$file title="Delete this file"|gettext onclick="return confirm('Are you sure you want to delete this file?');"}
					{/if}
				</div>
			{/permissions}
			<div class="bodycopy">
				{if $config.usestags}
					<div class="tags">
						Tags: 
						{foreach from=$file->expTag item=tag name=tags}
							<a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title}</a>
							{if $smarty.foreach.tags.last != 1},{/if}
						{/foreach} 
					</div>
				{/if}
				{if $config.usebody==1}
                    <p>{$file->body|summarize:"html":"paralinks"}</p>
                {elseif $config.usebody==2}
				{else}
                    {$file->body}
                {/if}
			</div>
			{if $config.usebody==1 || $config.usebody==2}
				<a class="readmore" href="{link action=show title=$file->sef_url}">{'Read more'|gettext}</a>
				&nbsp;&nbsp;
			{/if}
			{if !$config.quick_download}
				<a class="download" href="{link action=downloadfile fileid=$file->id}">{'Download'|gettext}</a>
			{/if}
            {if $config.show_player && ($filetype == "mp3" || $filetype == "flv" || $filetype == "f4v")}
                <a href="{$file->expFile.downloadable[0]->url}" style="display:block;width:360px;height:30px;" class="filedownloads-media"></a>
            {/if}
			{clear}
			{permissions}
				<div class="module-actions">
					{if $permissions.create == 1}
						{icon class=add action=edit title="Add a File Here" text="Add a File"|gettext}
					{/if}
				</div>
			{/permissions}
			{clear}  
		</div>		
    {/foreach}
    {pagelinks paginate=$page bottom=1}
</div>

{if $config.show_player}
    {script unique="filedownloads" src="`$smarty.const.PATH_RELATIVE`external/flowplayer3/example/flowplayer-3.2.6.min.js"}
    {literal}
    flowplayer("a.filedownloads-media", EXPONENT.PATH_RELATIVE+"external/flowplayer3/flowplayer-3.2.7.swf",
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
                    height: 30,
                    autoHide: false
                }
            }
        }
    );
    {/literal}
    {/script}
{/if}