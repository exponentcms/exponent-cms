{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
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

{css unique="photo-album" link="`$smarty.const.PATH_RELATIVE`framework/modules/photoalbum/assets/css/photoalbum.css"}

{/css}

<div class="module photoalbum showall">
    {if $moduletitle != ""}<h1>{$moduletitle}</h1>{/if}
    {permissions level=$smarty.const.UILEVEL_NORMAL}
        {if $permissions.create == 1}
            {icon class="add" action=edit rank=1 title="Add to the top" text="Add Image"}
        {/if}
        {if $permissions.edit == 1}
            {ddrerank items=$page->records model="photo"}
        {/if}
    {/permissions}

    {$page->links}    
    
    <ul>
    {foreach from=$page->records item=record name=items}
        <li style="width:{$config.pa_showall_thumbbox}px;height:{$config.pa_showall_thumbbox}px;">
            <a href="{link action=show title=$record->sef_url}" title="View more about {$record->title}">
                {img class="thumb" alt=$record->alt file_id=$record->expFile[0]->id w=$config.pa_showall_thumbbox h=$config.pa_showall_thumbbox zc=1}            
            </a>
            {permissions level=$smarty.const.UILEVEL_NORMAL}
                <div class="admin-actions">
                    {if $permissions.edit == 1}
                        {icon img=edit.png action=edit id=$record->id title="Edit `$modelname`"}
                    {/if}
                    {if $permissions.delete == 1}
                        {icon img=delete.png action=delete id=$record->id title="Delete `$modelname`"}
                    {/if}
                    {if $permissions.create == 1}
                        {icon img="add.png" action=edit rank=`$text->rank+1` title="Add another image after this one"}
                    {/if}
                </div>
            {/permissions}
        </li>
    {/foreach}
    </ul>
    
    {$page->links}
</div>
