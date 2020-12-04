{*
 * Copyright (c) 2004-2021 OIC Group, Inc.
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

{if $record->parent_id == 0}
    {control type="hidden" name="tab_loaded[categories]" value=1}
    {if count($record->childProduct)}
        <h4><em>({'Child products inherit these settings.'|gettext})</em></h4>
    {/if}
	{icon class="manage" controller="storeCategory" action="manage" text="Manage Store Categories"|gettext}
	{br}
	{control type="tagtree" name="managecats" id="managecats" controller="store" model="storeCategory" draggable=false addable=false menu=true checkable=true values=$record->storeCategory expandonstart=true }
{else}
	<h4><em>({'Categories'|gettext} {'are inherited from this product\'s parent.'|gettext})</em></h4>
{/if}