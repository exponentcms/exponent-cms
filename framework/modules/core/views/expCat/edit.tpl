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

<div class="expcat edit">
	<div class="form_header">
        {if $record->id == ""}
            <h1>{'Create'|gettext}{if !empty($model)} {$model|capitalize}{/if} {'Category'|gettext}</h1>
            <p>{'Create a new category to add to the list of available categories'|gettext}</p>
        {else}
            <h1>{'Edit'|gettext}{if !empty($model)} {$model|capitalize}{/if} {'Category'|gettext}</h1>
            <p>{'Edit this category to update all associated categorized items'|gettext}</p>
        {/if}
	</div>
	{form controller=expCat action=update}
		{control type=hidden name=id value=$record->id}
        {control type=hidden name=rank value=$record->rank}
		{control type=text name=title label="Category Name"|gettext value=$record->title}
        {control type=color name=color1 label="Color/Class"|gettext value='#012345' description='Enter a six-digit hex color preceded by #, or an \'a\' tag style name'|gettext}
        {if empty($model)}
            {control type="dropdown" name=module label="Associated Module"|gettext items=$mods value=$record->module}
        {else}
            {control type=hidden name=module value=$model}
        {/if}
		{control type=buttongroup submit="Save"|gettext cancel="Cancel"|gettext}
	{/form}
</div>
