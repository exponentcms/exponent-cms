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

{script unique="flowplayer" src="`$smarty.const.PATH_RELATIVE`external/flowplayer3/example/flowplayer-3.2.6.min.js"}
{literal}
flowplayer("a.flowplayer-video", EXPONENT.PATH_RELATIVE+"external/flowplayer3/flowplayer-3.2.7.swf",
    {
		wmode: 'opaque',
		clip: {
			autoPlay:{/literal}{if $config.autoplay}true{else}false{/if}{literal},
			},		  
        plugins:  { 
            controls: { 
                play: true,  
                scrubber: true 
            }         
        } 
    }
);
{/literal}

{/script}

<div class="module flowplayer showall">
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}
	{permissions}
		<div class="module-actions">
			{if $permissions.edit == 1}
				{icon class=add action=edit title="Add a Video at the Top"|gettext text="Add a Video"|gettext}
			{/if}
			{if $permissions.manage == 1}
				{ddrerank items=$page->records model="flowplayer" label="Videos"|gettext}
			{/if}
		</div>	
	{/permissions}   
    {pagelinks paginate=$page top=1}
    {foreach from=$page->records item=video key=key}
        <div class="item">
            <h2>{$video->title}</h2>
            <div class="video">
                <a href="{$video->expFile.video[0]->url}" style="display:block;width:{$video->width|default:200}px;height:{$video->height|default:143}px;" class="flowplayer-video">
                    {if $video->expFile.splash[0]->url}
                        {img file_id=$video->expFile.splash[0]->id w=$video->width h=$video->height zc=1}
                    {/if}
                </a> 
            </div>
			{permissions}
				<div class="item-actions">
					{if $permissions.edit == 1}
						{icon action=edit record=$video title="Edit `$video->title` video"}
					{/if}
					{if $permissions.delete == 1}
						{icon action=delete record=$video title="Delete `$video->title` video" onclick="return confirm('Are you sure you want to delete this `$modelname`?');"}
					{/if}
				</div>			
			{/permissions} 
            <div class="bodycopy">
                {$video->body}
            </div>            
        </div>
		{permissions}
			<div class="module-actions">		
				{if $permissions.create == 1}
					{icon class=add action=edit rank=$video->rank+1 title="Add a Video Here"|gettext text="Add a Video"|gettext}
				{/if}
			</div>
		{/permissions}
		{clear}
    {/foreach}
    {pagelinks paginate=$page bottom=1}
</div>
