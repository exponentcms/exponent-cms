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

<div class="module youtube showall">
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}

    {permissions level=$smarty.const.UILEVEL_NORMAL}
        <div class="moduleactions">
        {if $permissions.create == 1}
            {icon class="add" action=edit rank=1 title="Add to the top" text="Add a YouTube Video Here"}
        {/if}
        {if $permissions.edit == 1}
            {ddrerank items=$page->records model="portfolio" label="YouTube Videos"|gettext}
        {/if}
        </div>
    {/permissions}    

    {foreach from=$items item=ytv name=items}
        {if $ytv->title}<h2>{$ytv->title}</h2>{/if}

        {permissions}
            {if $permissions.edit == 1}
                {icon action=edit img=edit.png class="editlink" id=$ytv->id title="Edit this `$modelname`"}
            {/if}
            {if $permissions.delete == 1}
                {icon action=delete img=delete.png id=$ytv->id title="Delete this Video" onclick="return confirm('Are you sure you want to delete this YouTube Video?');"}
            {/if}
        {/permissions}

        <div class="embedcode">
            {$ytv->embed_code}
        </div>

        <div class="bodycopy">
            {$ytv->description}
        </div>
    {/foreach}

</div>
