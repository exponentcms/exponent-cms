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
    <h1>{$record->title}</h1>
    {printer_friendly_link}{export_pdf_link prepend='&nbsp;&nbsp;|&nbsp;&nbsp;'}{br}
    {assign var=myloc value=serialize($__loc)}
    <span class="date">{$record->publish_date|format_date:"%A, %B %e, %Y"}</span>
    {if $record->expTag|@count>0 && !$config.disabletags}
        <div class="tags">
            {"Tags"|gettext}:
            {foreach from=$record->expTag item=tag name=tags}
                <a href="{link action=showall_by_tags tag=$tag->sef_url}">{$tag->title}</a>{if $smarty.foreach.tags.last != 1},{/if}
            {/foreach}
        </div>
    {/if}
    {permissions}
        <div class="item-actions">   
            {if $permissions.edit == true}
                {if $myloc != $record->location_data}
                    {if $permissions.manage == 1}
                        {icon action=merge id=$record->id title="Merge Aggregated Content"|gettext}
                    {else}
                        {icon img='arrow_merge.png' title="Merged Content"|gettext}
                    {/if}
                {/if}
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
