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
    {if $config.enable_rss == true}
        <a class="rsslink" href="{rsslink}" title="{'Subscribe to'|gettext} {$config.feed_title}"></a>
    {/if}
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
                {if $rank == 1}
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
    {pagelinks paginate=$page top=1}
    {assign var="cat" value="bad"}
    {foreach from=$page->records item=file name=files}
        {if $cat != $file->expCat[0]->id && $config.usecategories}
            <a href="{link action=showall src=$page->src group=$file->expCat[0]->id}" title='View this group'|gettext><h2 class="category">{if $file->expCat[0]->title!= ""}{$file->expCat[0]->title}{elseif $config.uncat!=''}{$config.uncat}{else}{'Uncategorized'|gettext}{/if}</h2></a>
        {/if}
        {include 'filedownloaditem.tpl'}
        {assign var="cat" value=$file->expCat[0]->id}
    {/foreach}
    {pagelinks paginate=$page bottom=1}
</div>

{if $config.show_player}
    {script unique="filedownload" src="`$smarty.const.FLOWPLAYER_PATH`flowplayer-`$smarty.const.FLOWPLAYER_MIN_VERSION`.min.js"}
    {literal}
    flowplayer("a.filedownload-media", EXPONENT.FLOWPLAYER_PATH+"flowplayer-"+EXPONENT.FLOWPLAYER_VERSION+".swf",
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