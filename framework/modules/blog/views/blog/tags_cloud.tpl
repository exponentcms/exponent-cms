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

<div class="module blog tags_cloud">
    {if $moduletitle}<h2>{$moduletitle}</h2>{/if}
    {permissions}
        {if $permissions.edit == 1}
            {icon class=add action=edit text="Add a new blog article"|gettext}
        {/if}
        {if $permissions.manage == 1}
            {icon controller=expTag action=manage text="Manage Tags"|gettext}
        {/if}
        {br}
    {/permissions}
    <ul>
        {foreach from=$tags item=tag}
            <li>
                <a href="{link action=showall_by_tags tag=$tag->sef_url}" style="font-size:1.{if $tag->count<10}0{$tag->count}{else}{$tag->count}{/if}em;">{$tag->title}</a>
            </li>
        {/foreach}
    </ul>
</div>
