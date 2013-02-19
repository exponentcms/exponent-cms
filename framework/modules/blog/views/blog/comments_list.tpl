{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<h2>{$moduletitle}</h2>{/if}
    {permissions}
        {if $permissions.manage == 1}
            {icon controller=expComment action=manage text="Manage Comments"|gettext}
        {/if}
    {/permissions}
    {if $config.moduledescription != ""}
   		{$config.moduledescription}
   	{/if}
    <ul>
        {foreach from=$comments item=comment}
            <li>
                <a href="{link action=show title="`$comment->sef_url`"}#exp-comments" title="{$comment->body|summarize:"html":"para"}">{$comment->name} {'on'|gettext} {$comment->ref}</a>
            </li>
        {/foreach}
    </ul>
</div>
