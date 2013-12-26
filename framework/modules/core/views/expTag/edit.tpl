{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

<div class="exptag edit">
	<div class="form_header">
        {if $record->id == ""}
            <h1>{'Create Tag'|gettext}</h1>
            <blockquote>{'Create a new tag to add to the list of available tags'|gettext}</blockquote>
        {else}
            <h1>{'Edit Tag'|gettext}</h1>
            <blockquote>{'Edit this tag to update all associated tagged items'|gettext}</blockquote>
        {/if}
    </div>
	{form controller=expTag action=update}
		{control type=hidden name=id value=$record->id}
		{*{control type=hidden name=tag_collections_id value=$record->tag_collections_id}*}
		{*{control type=hidden name=parent_id value=$record->parent_id}*}
		{*{control type=hidden name=rgt value=$record->rgt}*}
		{*{control type=hidden name=lft value=$record->lft}*}
		{control type=text name=title label="Tag Name"|gettext value=$record->title}
		{control type=buttongroup submit="Save"|gettext cancel="Cancel"|gettext}
	{/form}
</div>

