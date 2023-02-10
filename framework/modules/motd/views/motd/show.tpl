{*
 * Copyright (c) 2004-2023 OIC Group, Inc.
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

{clear}
<div class="module motd show">
    {if !empty($message)}
    {if !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle|default:"Message of the Day"|gettext}</{$config.heading_level|default:'h1'}>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {$myloc=serialize($__loc)}
    <div class="motd-message">
        {if $config.datetag}
            <p class="post-date">
                <span class="month">{$now|format_date:"%b"}</span>
                <span class="day">{$now|format_date:"%e"}</span>
                <span class="year">{$now|format_date:"%Y"}</span>
            </p>
        {else}
            <div class="motd-date">
                <span class="date-header">{$now|format_date}</span>
                {clear}
            </div>
        {/if}
        <div class="bodycopy">
            {$message->body}
        </div>
        {clear}
        {permissions}
            <div class="item-actions">
                {if $permissions.edit || ($permissions.create && $message->poster == $user->id)}
                    {if $myloc != $message->location_data}
                        {if $permissions.manage}
                            {icon action=merge id=$message->id title="Merge Aggregated Content"|gettext}
                        {else}
                            {icon img='arrow_merge.png' title="Merged Content"|gettext}
                        {/if}
                    {/if}
                    {icon action=edit record=$message title="Edit this tip"|gettext}
                {/if}
                {if $permissions.delete || ($permissions.create && $message->poster == $user->id)}
                    {icon action=delete record=$message title="Delete this tip"|gettext onclick="return confirm('"|cat:("Are you sure you want to delete this tip?"|gettext)|cat:"');"}
                {/if}
            </div>
        {/permissions}
    </div>
    {icon class=view action=showall_year text=$config.viewall|default:'View Other Tips'|gettext}
    {/if}
    {permissions}
        {if $permissions.edit || ($permissions.create && $message->poster == $user->id)}
            <div class="module-actions">
                {icon class=add action=edit text="Add a tip"|gettext}
            </div>
        {/if}
    {/permissions}
</div>
