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

{css unique="cloud-tags"}
.module.cloud a.ctag,
.module.cloud span.ctag {
	margin-left: 5px;
    margin-right: 5px;
}
{/css}

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
            {$tagt = str_replace(' ', "&#160;", $listing->title)}
            <a href="{link controller=expTag action=show title=$listing->sef_url}" class="ctag" style="font-size:{if $listing->attachedcount>99}2.0{else}1.{if $listing->attachedcount<10}0{$listing->attachedcount}{else}{$listing->attachedcount}{/if}{/if}em;" title="{'View items tagged with'|gettext} '{$listing->title}'">{$tagt}</a>
        {/foreach}
    </div>
</div>
