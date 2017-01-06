{*
 * Copyright (c) 2004-2017 OIC Group, Inc.
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
    {if !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle|default:"Message of the Day"|gettext}</{$config.heading_level|default:'h1'}>{/if}
    {$myloc=serialize($__loc)}
    <div class="motd-message">
        {if $config.datetag}
            <p class="post-date">
                <span class="month">{$smarty.now|format_date:"%b"}</span>
                <span class="day">{$smarty.now|format_date:"%e"}</span>
                <span class="year">{$smarty.now|format_date:"%Y"}</span>
            </p>
        {else}
            <div class="motd-date">
                <span class="date-header">{$smarty.now|format_date}</span>
                {clear}
            </div>
        {/if}
        <div class="bodycopy">
            {$message->body}
        </div>
        {clear}
        <a class="link" href="{link action=showall}">{'View Previous Tips'|gettext}</a>
        {permissions}
			<div class="module-actions">
				{if $permissions.edit || ($permissions.create && $message->poster == $user->id)}
                    {if !empty($message->location_data) && $myloc != $message->location_data}
                        {if $permissions.manage}
                            {icon action=merge id=$message->id title="Merge Aggregated Content"|gettext}
                        {else}
                            {icon img='arrow_merge.png' title="Merged Content"|gettext}
                        {/if}
                    {/if}
					{icon class=add action=edit text="Add a tip"|gettext}
			    {/if}
			 </div>
        {/permissions}
    </div>
</div>
