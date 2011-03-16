{*
 * Copyright (c) 2004-2008 OIC Group, Inc.
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

<div class="module blog showall-headlines">
    {if $moduletitle}<h2>{$moduletitle}</h2>{/if}
    
    {permissions}
        {if $permissions.edit == 1}
            {icon class="add" action=edit title="Add a new blog article" text="Add a new blog article"}
      {/if}
    {/permissions}
    <ul>
    {foreach from=$page->records item=record name="blogs"}
        {if $smarty.foreach.blogs.iteration <= $config.headcount}
        <li class="bodycopy">
            <a href="{link action=show title=$record->sef_url}">{$record->title}</a>
            {permissions}
                <div class="item-actions">
                    {if $permissions.edit == 1}
                        {icon action=edit img=edit.png class="editlink" id=$record->id title="Edit this `$modelname`"}
                    {/if}
                    {if $permissions.delete == 1}
                        {icon action=delete img=delete.png id=$record->id title="Delete this `$modelname`" onclick="return confirm('Are you sure you want to delete this `$modelname`?');"}
                    {/if}
                </div>
            {/permissions}
        </li>
        {/if}
    {/foreach}
    </ul> 
</div>
