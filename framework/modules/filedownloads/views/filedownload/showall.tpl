{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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

<div class="module filedownload showall-downloadinfo">
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}
    
    {if $config.enable_rss}
        <a class="podcastlink" href="{podcastlink}">Subscribe to {$config.feed_title}</a>
    {/if}

    {$page->links}
    {foreach from=$page->records item=file name=files}
        {if $file->expFile.preview[0] != ""}
            {img class="preview-img" file_id=$file->expFile.preview[0]->id square=150}
        {/if}
        {if $file->title}<h2>{$file->title}</h2>{/if}
        <span class="label size">File Size:</span>
        <span class="value">{$file->expFile.downloadable[0]->filesize|kilobytes}Kb</span>
        &nbsp;&nbsp;
        <span class="label downloads"># Downloads:</span>
        <span class="value">{$file->downloads}</span>
        <div class="bodycopy">
            {$file->body}
        </div>
        <a class="readmore" href="{link action=show title=$file->sef_url}">Read more</a>
        &nbsp;&nbsp;
        <a class="download" href="{link action=downloadfile fileid=$file->id}">Download</a>
        {clear}
        {permissions level=$smarty.const.UILEVEL_NORMAL}
            {if $permissions.edit == 1}
                {icon action=edit img=edit.png class="editlink" id=$file->id title="Edit this file"}
            {/if}
            {if $permissions.delete == 1}
                {icon action=delete img=delete.png id=$file->id title="Delete this file" onclick="return confirm('Are you sure you want to delete this file?');"}
            {/if}
            {if $permissions.edit == 1}
                {if $smarty.foreach.files.first == 0}
                    {icon controller=filedownload action=rerank img=up.png id=$file->id push=up}    
                {/if}
                {if $smarty.foreach.files.last == 0}
                    {icon controller=filedownload action=rerank img=down.png id=$file->id push=down}
                {/if}
            {/if}
        {/permissions}
        {clear}        
    {/foreach}
    {$page->links}
    
    {permissions level=$smarty.const.UILEVEL_NORMAL}
        {if $permissions.create == 1}
            {icon class=add action=edit title="Add a File" text="Add a File for Download"}
        {/if}
    {/permissions}
</div>
