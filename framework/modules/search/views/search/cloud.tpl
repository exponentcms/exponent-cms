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

{css unique="blog" link="`$asset_path`css/blog.css"}

{/css}

<div class="module search cloud">
    {if $moduletitle}<h2>{$moduletitle}</h2>{/if}
    {permissions}
        {if $permissions.manage == 1}
            {icon class="manage" controller=expTag action=manage text="Manage Tags"|gettext}
        {/if}
        {br}
    {/permissions}
    {foreach from=$page->records item=listing}
        <a href="{link controller=expTag action=show title=$listing->sef_url}" style="font-size:1.{if $listing->attachedcount<10}0{$listing->attachedcount}{else}{$listing->attachedcount}{/if}em;">{$listing->title}</a>
    {/foreach}
</div>
