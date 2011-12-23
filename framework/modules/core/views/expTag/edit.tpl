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

<div class="exptag edit">
	<div class="form_header">
        <h1>{'Edit Tags'|gettext}</h1>
        <p>{'Add new tags to this tag collection'|gettext}</p>
	</div>
	{if $record->id == ""}
		{assign var=action value=create}
	{else}
		{assign var=action value=update}
	{/if}
	
	{form controller=expTag action=$action}
		{control type=hidden name=id value=$record->id}
		{control type=hidden name=tag_collections_id value=$record->tag_collections_id}
		{control type=hidden name=parent_id value=$record->parent_id}
		{control type=hidden name=rgt value=$record->rgt}
		{control type=hidden name=lft value=$record->lft}
		{control type=text name=title label="Tag"|gettext value=$record->title}
		{control type=buttongroup submit="Save"|gettext cancel="Cancel"|gettext}
	{/form}
</div>

