{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

<div class="module blog comments_list">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h2'}>{$moduletitle}</{$config.heading_level|default:'h2'}>{/if}
    {permissions}
        {if $permissions.manage}
            {icon controller=expComment action=manage text="Manage Comments"|gettext}
        {/if}
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    <ul>
        {foreach from=$comments item=comment}
            <li>
                {if !$config.hidedate}
                    <em class="date">{$comment->created_at|relative_date}</em>{br}
                {/if}
                {if !$config.displayauthor}
                    <a href="{link action=show title="`$comment->sef_url`"}#exp-comments" title="{$comment->body|summarize:"html":"para"}">{$comment->name} {'commented on'|gettext} {$comment->ref}</a>
                {else}
                    <a href="{link action=show title="`$comment->sef_url`"}#exp-comments" title="{$comment->body|summarize:"html":"para"}">{'Comment on'|gettext} {$comment->ref}</a>
                {/if}
            </li>
        {/foreach}
    </ul>
</div>
