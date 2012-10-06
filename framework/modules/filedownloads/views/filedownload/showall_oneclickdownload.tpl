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

{if $config.usecategories}
{css unique="categories" corecss="categories"}

{/css}
{/if}

<div class="module filedownload showall">
    {if $moduletitle && !$config.hidemoduletitle}<h1>{/if}
    {rss_link}
    {if $moduletitle && !$config.hidemoduletitle}{$moduletitle}</h1>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit rank=1 title="Add a File at the Top"|gettext text="Add a File"|gettext}
			{/if}
            {if $permissions.manage == 1}
                {if !$config.disabletags}
                    {icon controller=expTag class="manage" action=manage_module model='filedownload' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    {icon controller=expCat action=manage model='filedownload' text="Manage Categories"|gettext}
                {/if}
                {*{if $rank == 1}*}
                {if $config.order == 'rank'}
                    {ddrerank items=$page->records model="filedownload" label="Downloadable Items"|gettext}
                {/if}
           {/if}
        </div>
    {/permissions}    
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {subscribe_link}
    {assign var=myloc value=serialize($__loc)}
    {assign var="cat" value="bad"}
    {foreach from=$page->records item=file name=files}
        {if $cat !== $file->expCat[0]->id && $config.usecategories}
            <h2 class="category">{if $file->expCat[0]->title!= ""}{$file->expCat[0]->title}{elseif $config.uncat!=''}{$config.uncat}{else}{'Uncategorized'|gettext}{/if}</h2>
		{/if}
			
		<div class="item">
			{$filetype=$file->expFile.downloadable[0]->filename|regex_replace:"/^.*\.([^.]+)$/D":"$1"}
			{if $file->expFile.preview[0] != "" && $config.show_icon}
				{img class="preview-img" file_id=$file->expFile.preview[0]->id square=150}
			{/if}
			
			<h3>{icon action=downloadfile fileid=$file->id title='Download'|gettext text=$file->title}</h3>

			{permissions}
				<div class="item-actions">
					{if $permissions.edit == 1}
						{if $myloc != $file->location_data}
							{if $permissions.manage == 1}
								{icon action=merge id=$file->id title="Merge Aggregated Content"|gettext}
							{else}
								{icon img='arrow_merge.png' title="Merged Content"|gettext}
							{/if}
						{/if}
						{icon action=edit record=$file title="Edit this file"|gettext}
					{/if}
					{if $permissions.delete == 1}
						{icon action=delete record=$file title="Delete this file"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this file?"|gettext)|cat:"');"}
					{/if}
				</div>
			{/permissions}

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
        {assign var="cat" value=$file->expCat[0]->id}
    {/foreach}
    
</div>

{if $config.show_player}
    {script unique="filedownload" src="`$smarty.const.FLOWPLAYER_RELATIVE`flowplayer-`$smarty.const.FLOWPLAYER_MIN_VERSION`.min.js"}
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