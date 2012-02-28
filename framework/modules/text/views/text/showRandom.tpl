{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

<div class="module text show-random">
	    {if $moduletitle && !$config.hidemoduletitle}<h1>{$moduletitle}</h1>{/if}
        {assign var=myloc value=serialize($__loc)}
	    {permissions}
    	    <div class="module-actions">
				{if $permissions.create == 1}
					{icon class=add action=edit text="Add Text"|gettext}
				{/if}
				{if $permissions.edit == 1}
					{br}{icon class=manage action=showall text="Manage Text Items"|gettext}
				{/if}
            </div>
        {/permissions}
        {foreach from=$items item=listing}
			{if $listing->title}<h2><a href="{link controller=$controller action=show id=$listing->id}">{$listing->title}</a></h2>{/if}
			{permissions}
				<div class="item-actions">
                    {if $myloc != $listing->location_data}{icon class=merge img='arrow_merge.png' title="Aggregated Content"|gettext}{/if}
					{if $permissions.edit == 1}
						{icon action=edit record=$listing}
					{/if}
					{if $permissions.delete == 1}
						{icon action=delete record=$listing}
					{/if}
				</div>
			{/permissions}
    		<div class="bodycopy">
    		    {$listing->body}
    		</div>
        {/foreach}
</div>
