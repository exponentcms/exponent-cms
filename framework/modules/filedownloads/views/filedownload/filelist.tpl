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

    {$myloc=serialize($__loc)}
    {pagelinks paginate=$page top=1}
    {$cat="bad"}
    {foreach from=$page->records item=file name=files}
        {if $cat !== $file->expCat[0]->id && $config.usecategories}
            <a href="{link action=showall src=$page->src group=$file->expCat[0]->id}" title='View this group'|gettext><h2 class="category">{if $file->expCat[0]->title!= ""}{$file->expCat[0]->title}{elseif $config.uncat!=''}{$config.uncat}{else}{'Uncategorized'|gettext}{/if}</h2></a>
        {/if}
        {include 'filedownloaditem.tpl'}
        {$cat=$file->expCat[0]->id}
    {/foreach}
    {pagelinks paginate=$page bottom=1}

{*{if $config.show_player}*}
    {*{script unique="filedownload"}*}
    {*{literal}*}
    {*flowplayer("a.filedownload-media", EXPONENT.FLOWPLAYER_RELATIVE+"flowplayer-"+EXPONENT.FLOWPLAYER_VERSION+".swf",*}
        {*{*}
    		{*wmode: 'transparent',*}
    		{*clip: {*}
    			{*autoPlay: false,*}
    			{*},*}
            {*plugins:  {*}
                {*controls: {*}
                    {*play: true,*}
                    {*scrubber: true,*}
                    {*fullscreen: false,*}
                    {*autoHide: false*}
                {*}*}
            {*}*}
        {*}*}
    {*);*}
    {*{/literal}*}
    {*{/script}*}
{*{/if}*}
