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
<div class="module links showall-links">
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}
    {permissions level=$smarty.const.UILEVEL_NORMAL}
    <div class="module-actions">
        {if $permissions.create == 1 || $permissions.edit == 1}
            {icon controller=links class="add" action=create text="Create new link" title="Create a new link"}
            {ddrerank label="Links" items=$items}
        {/if}
    </div>
    {/permissions}
    
        {foreach name=items from=$items item=item}
        <div class="item">
                
            <h2><a class="li-link" {if $item->new_window}target="_blank"{/if} href="{$item->url}">{$item->title}</a></h2>
            {permissions level=$smarty.const.UILEVEL_NORMAL}
                <div class="actions">
                {if $permissions.edit == 1}
                    {icon controller=links action=edit id=$item->id title="Edit this `$modelname`"}
                {/if}
                {if $permissions.delete == 1}
                    {icon controller=links action=delete id=$item->id title="Delete this `$modelname`" onclick="return confirm('Are you sure you want to delete this `$modelname`?');"}
                {/if}
                {if $permissions.edit == 1}
                    {if $smarty.foreach.items.first == 0}
                        {icon controller=links action=rerank img=up.png id=$item->id push=up}    
                    {/if}
                    {if $smarty.foreach.items.last == 0}
                        {icon controller=links action=rerank img=down.png id=$item->id push=down}
                    {/if}
                {/if}
                </div>
                
            {/permissions}

            {if $item->expFile[0]->id}
                <a class="li-link" {if $item->new_window}target="_blank"{/if} href="{$item->url}">{img file_id=$item->expFile[0]->id width=200 height=150 constrain=1 style="float:left; margin-right:10px"}</a>
            {/if}
            {if $item->body}
            <div class="bodycopy">
                {$item->body}
            </div>
            {/if}
            <div style="clear:both"></div>
        </div>
        {/foreach}
    </ul>
</div>
