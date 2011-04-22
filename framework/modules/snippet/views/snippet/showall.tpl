{*
 * Copyright (c) 2007-2011 OIC Group, Inc.
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

<div class="module snippet showall">
    {if $moduletitle}<h1>{$moduletitle}</h1>{/if}

    {assign var=text value=$page->records[0]}
    {if $text->title}<h2>{$text->title}</h2>{/if}
    {permissions}
	<div class="item-actions">
        {if $permissions.edit == 1}
            {if $text->id != ""}
                {icon action=edit img=edit.png class="editlink" id=$text->id title="Edit this code snippet" text="Edit this code snippet"}
            {else}
                {icon action=edit class="add" title="Add a new code snippet" text="Add a new code snippet"}
            {/if}              
        {/if}
        {if $permissions.delete == 1 && $text->id != ""}
            {icon action=delete img=delete.png id=$text->id title="Delete this `$modelname`" onclick="return confirm('Are you sure you want to delete this `$modelname`?');"}
        {/if}
	</div>
	<div class="module-actions">
        {if $permissions.edit == true}
            {if $smarty.foreach.items.first == 0}
                {icon controller=text action=rerank img=up.png id=$text->id push=up}    
            {/if}
            {if $smarty.foreach.items.last == 0}
                {icon controller=text action=rerank img=down.png id=$text->id push=down}
            {/if}
        {/if}
	</div>
    {/permissions}
    <div class="bodycopy">
        {$text->body}
        {clear}
    </div>

</div>
