{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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

<div class="module search cloud">
    {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}<{$config.heading_level|default:'h2'}>{$moduletitle}</{$config.heading_level|default:'h2'}>{/if}
    {permissions}
        <div class="module-actions">
            {if $permissions.manage}
                {icon controller=expTag action=manage text="Manage Tags"|gettext}
            {/if}
        </div>
    {/permissions}
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    <div class="item">
        {foreach from=$page->records item=listing}
            <a href="{link controller=expTag action=show title=$listing->sef_url}" style="font-size:{if $listing->attachedcount>99}2.0{else}1.{if $listing->attachedcount<10}0{$listing->attachedcount}{else}{$listing->attachedcount}{/if}{/if}em;" title="{'View items tagged with'|gettext} '{$listing->title}'">{$listing->title}</a>
        {/foreach}
    </div>
</div>
