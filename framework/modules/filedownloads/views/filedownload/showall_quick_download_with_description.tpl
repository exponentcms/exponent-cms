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

<div class="module filedownload quick-with-description">
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}
    {if $config.enable_rss}
        <a class="podcastlink" href="{podcastlink}">Subscribe to {$config.feed_title}</a>
    {/if}
    {$page->links}
    {permissions}
        <div class="module-actions">
			{if $permissions.create == 1}
				{icon class="add" action=edit rank=1 title="Add a File at the Top"|gettext text="Add a File"|gettext}
			{/if}
			{if ($permissions.edit == 1 && $order != 'created_at')}
				{ddrerank items=$page->records model="filedownload" label="Downloadable Items"|gettext}
			{/if}
        </div>
    {/permissions}    
    
    {foreach from=$page->records item=file name=files}
       <div class="item">
            {if $file->expFile.preview[0] != ""}
                {img class="preview-img" file_id=$file->expFile.preview[0]->id square=150}
            {/if}
            <h2><a class="download" href="{link action=downloadfile fileid=$file->id}">{$file->title}</a></h2>
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
                <div class="tags">
                    Tags: 
                    {foreach from=$file->expTag item=tag name=tags}
                    <a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title}</a>
                    {if $smarty.foreach.tags.last != 1},{/if}
                    {/foreach} 
                </div>
                
                {$file->body}
            </div>
        </div>
		{permissions}
			<div class="module-actions">
				{if $permissions.create == 1}
					{icon class=add action=edit title="Add a File Here"|gettext text="Add a File"|gettext}
				{/if}
			</div>
		{/permissions}
		{clear}
    {/foreach}
    {$page->links}
</div>
