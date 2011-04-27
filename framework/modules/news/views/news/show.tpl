{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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
        <div class="item-actions">   
            {if $permissions.edit == true}
                {icon controller=news action=edit record=$record title="Edit this news post"}
            {/if}
            {if $permissions.delete == true}
                {icon controller=news action=delete record=$record title="Delete this news post" onclick="return confirm('Are you sure you want to delete `$record->title`?');"}
            {/if}
        </div>
    {/permissions}
    <div class="bodycopy">
        {filedisplayer view="`$config.filedisplay`" files=$record->expFile record=$record}
        {$record->body}
    </div>
</div>
