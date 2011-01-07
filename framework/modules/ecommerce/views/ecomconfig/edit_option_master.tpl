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

{permissions level=$smarty.const.UILEVEL_NORMAL}
{if $permissions.manage == 1}
<div class="module storeadmin edit_option_master">
	<h1>{$moduletitle|default:"Edit Product Options"}</h1>
	{if $record->timesImplemented > 0}
	<p>
	    This option is being used by {$record->timesImplemented} products on your site.  Changing the name will change it for all the products currently using it.
	</p>
	{/if}
	{form action=update_option_master}
	    {control type="hidden" name=id value=$record->id}
        {control type="hidden" name=rank value=$record->rank}
	    {control type="hidden" name=optiongroup_master_id value=$record->optiongroup_master_id}
	    {control type="text" name="title" label="Name" value=$record->title}
	    {control type="buttongroup" submit="Submit" cancel="Cancel"}
	{/form}
</div>
{/if}
{/permissions}
