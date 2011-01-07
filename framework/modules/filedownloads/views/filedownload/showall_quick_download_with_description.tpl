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

<div class="module filedownload quick-with-description">
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}
    {permissions level=$smarty.const.UILEVEL_NORMAL}
        <div class="moduleactions">
        {if $permissions.create == 1}
            {icon class="add" action=edit rank=1 title="Add to the top" text="Add a File"}
        {/if}
        {if $permissions.edit == 1}
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
            {permissions level=$smarty.const.UILEVEL_NORMAL}
            <div class="item-actions">
                {if $permissions.edit == 1}
                    {icon action=edit img=edit.png class="editlink" id=$file->id title="Edit this file"}
                {/if}
                {if $permissions.delete == 1}
                    {icon action=delete img=delete.png id=$file->id title="Delete this file" onclick="return confirm('Are you sure you want to delete this file?');"}
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
    {/foreach}
    {permissions level=$smarty.const.UILEVEL_NORMAL}
        {if $permissions.create == 1}
            {icon class=add action=edit title="Add a File" text="Add a File for Download"}
        {/if}
    {/permissions}
</div>
