{*
 * Copyright (c) 2007-2008 OIC Group, Inc.
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
<div class="module links quicklinks">
    {if $moduletitle}<h2>{$moduletitle}</h2>{/if}
    {permissions level=$smarty.const.UILEVEL_NORMAL}
        <div class="module-actions">
        {if $permissions.create == 1 || $permissions.edit == 1}
            {icon controller=links class="add" action=create text="Create new link" title="Create a new link"}
        {/if}
        </div>
    {/permissions}
    <ul>
        {foreach name=items from=$items item=item}
        <li class="item{if $smarty.foreach.items.last} last{/if}">
            <a class="link" href="{$item->url}">{$item->title}</a>
            {permissions level=$smarty.const.UILEVEL_NORMAL}
                <div class="item-actions">
                {if $permissions.edit == 1}
                    {icon controller=links action=edit id=$item->id title="Edit this `$modelname`"}
                {/if}
                {if $permissions.delete == 1}
                    {icon controller=links action=delete id=$item->id title="Delete this `$modelname`" onclick="return confirm('Are you sure you want to delete this `$modelname`?');"}
                {/if}
                </div>
                
            {/permissions}
        </li>
        {/foreach}
    </ul>
</div>
