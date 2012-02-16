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

<div class="module news show">
    {if $config.printlink}
    {printer_friendly_link}
    {/if}
    <h1>{$record->title}</h1>
    <span class="date">{$record->publish|format_date:"%A, %B %e, %Y"}</span>
    {permissions}
        <div class="item-actions">   
            {if $permissions.edit == true}
                {icon action=edit record=$record}
            {/if}
            {if $permissions.delete == true}
                {icon action=delete record=$record}
            {/if}
        </div>
    {/permissions}
    <div class="bodycopy">
        {if $config.filedisplay != "Downloadable Files"}
            {filedisplayer view="`$config.filedisplay`" files=$record->expFile record=$record}
        {/if}
        {$record->body}
        {if $config.filedisplay == "Downloadable Files"}
            {filedisplayer view="`$config.filedisplay`" files=$record->expFile record=$record}
        {/if}
    </div>
</div>
