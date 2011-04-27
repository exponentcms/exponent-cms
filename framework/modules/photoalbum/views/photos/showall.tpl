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

{css unique="photo-album" link="`$smarty.const.PATH_RELATIVE`framework/modules/photoalbum/assets/css/photoalbum.css"}

{/css}

<div class="module photoalbum showall">
    {if $moduletitle != ""}<h1>{$moduletitle}</h1>{/if}
    {permissions}
		<div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit rank=1 title="Add to the top" text="Add Image"}
			{/if}
			{if $permissions.edit == 1}
				{ddrerank items=$page->records model="photo"}
			{/if}
		</div>
    {/permissions}
    {pagelinks paginate=$page top=1}
    <ul>
    {foreach from=$page->records item=record name=items}
        <li style="width:{$config.pa_showall_thumbbox}px;height:{$config.pa_showall_thumbbox}px;">
            <a href="{link action=show title=$record->sef_url}" title="View more about {$record->title}">
                {img class="thumb" alt=$record->alt file_id=$record->expFile[0]->id w=$config.pa_showall_thumbbox h=$config.pa_showall_thumbbox zc=1}            
            </a>
            {permissions}
                <div class="item-actions">
                    {if $permissions.edit == 1}
                        {icon action=edit record=$record title="Edit `$modelname`"}
                    {/if}
                    {if $permissions.delete == 1}
                        {icon action=delete record=$record title="Delete `$modelname`"}
                    {/if}
                    {if $permissions.create == 1}
						{icon class=add action=edit rank=`$text->rank+1` title="Add another image after this one"}
                    {/if}
                </div>
            {/permissions}
        </li>
    {/foreach}
    </ul>
    {pagelinks paginate=$page bottom=1}
</div>
