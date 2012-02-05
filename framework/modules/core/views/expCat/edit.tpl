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

<div class="expcat edit">
	<div class="form_header">
        {if $record->id == ""}
            <h1>{'Create Category'|gettext}</h1>
            <p>{'Create a new category to add to the list of available categories'|gettext}</p>
        {else}
            <h1>{'Edit Category'|gettext}</h1>
            <p>{'Edit this category to update all associated categorized items'|gettext}</p>
        {/if}
	</div>
	{form controller=expCat action=update}
		{control type=hidden name=id value=$record->id}
        {control type=hidden name=rank value=$record->rank}
		{*{control type=hidden name=cat_collections_id value=$record->cat_collections_id}*}
		{*{control type=hidden name=parent_id value=$record->parent_id}*}
		{*{control type=hidden name=rgt value=$record->rgt}*}
		{*{control type=hidden name=lft value=$record->lft}*}
		{control type=text name=title label="Category Name"|gettext value=$record->title}
        {control type=text name=color label="Color"|gettext value=$record->color}
        {control type=text name=module label="Module Specific?"|gettext value=$record->module}
		{control type=buttongroup submit="Save"|gettext cancel="Cancel"|gettext}
	{/form}
</div>

