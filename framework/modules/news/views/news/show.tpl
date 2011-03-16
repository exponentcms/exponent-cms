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

<div class="module news show">
    {if $config.printlink}
    {printer_friendly_link}
    {/if}
    <h1>{$record->title}</h1>
    <span class="date">{$record->publish|format_date:"%A, %B %e, %Y"}</span>
    {permissions}
        <div class="itempermissions">   
            {if $permissions.edit == true}
                {icon controller=news action=edit id=$record->id title="Edit this news post"}
            {/if}
            {if $permissions.delete == true}
                {icon controller=news action=delete id=$record->id title="Delete this news post" onclick="return confirm('Are you sure you want to delete `$record->title`?');"}
            {/if}
        </div>
    {/permissions}
    <div class="bodycopy"> 
        <div class="news-img">
            {if $record->expFile[1]->id}
                {foreach from=$record->expFile item=img key=key name=thumbs}
                    {if $smarty.foreach.thumbs.iteration%4==0}
                        {assign var="style" value="margin-right:0"}
                    {else}
                        {assign var="style" value=""}
                    {/if}
                    {img id="thumb-`$img->id`" class="thumbnail" alt=$img->alt file_id=$img->id w=150 style=$style}
                {/foreach}
            {/if}
        </div>
        
        {$record->body}
        {clear}
    </div>
</div>
