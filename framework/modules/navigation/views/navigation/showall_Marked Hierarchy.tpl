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

<div class="module navigation marked-hierarchy">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h1'}>{$moduletitle}</{$config.heading_level|default:'h1'}>{/if}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    <ul>
        {foreach from=$sections item=section}
            <li style="margin-left: {$section->depth*20}px">
                {if $section->id == $current->id}
                    <strong><img src="{$smarty.const.ICON_RELATIVE|cat:'mark.gif'}" title="{'You are here'|gettext}" alt="{'Mark'|gettext}" />
                {/if}
                {if $section->active == 1}
                    <a href="{$section->link}" class="navlink"{if $section->new_window} target="_blank"{/if}>{$section->name}</a>&#160;
                {else}
                    <span class="navlink">{$section->name}</span>&#160;
                {/if}
                {if $section->id == $current->id}
                    </strong>
                {/if}
            </li>
        {/foreach}
    </ul>
    {permissions}
        {if $canManage == 1}
            {icon action=manage}
        {/if}
    {/permissions}
</div>
