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
    {if $moduletitle && !$config.hidemoduletitle}<h1>{/if}
    {if $config.enable_rss == true}
        <a class="rsslink" href="{rsslink}" title="{'Subscribe to'|gettext} {$config.feed_title}"></a>
    {/if}
    {if $moduletitle && !$config.hidemoduletitle}{'Recent'|gettext} {$moduletitle}</h1>{/if}
    {permissions}
        <div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit rank=1 title="Add a File at the Top"|gettext text="Add a File"|gettext}
			{/if}
            {if $permissions.manage == 1}
                {icon controller=expTag action=manage text="Manage Tags"|gettext}
            {/if}
			{if ($permissions.manage == 1 && $rank == 1)}
				{ddrerank items=$page->records model="filedownload" label="Downloadable Items"|gettext}
			{/if}
        </div>
    {/permissions}    
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    {assign var="cat" value="bad"}
    {foreach from=$page->records item=file name=files}
        {if $smarty.foreach.files.iteration<=$config.headcount || !$config.headcount}
        {if $cat != $file->expCat[0]->id && $config.usecategories}
            {if $file->expCat[0]->title!= ""}from "{$file->expCat[0]->title}" dated {$file->created_at|format_date}{/if}
        {/if}
        {include 'filedownloaditem.tpl'}
        {assign var="cat" value=$file->expCat[0]->id}
        {/if}
    {/foreach}
    {if $page->total_records > $config.headcount}
        {br}{icon action="showall" text="More Items in '`$moduletitle`' ..."|gettext}
    {/if}
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