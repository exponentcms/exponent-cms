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

{get_user assign=user}
{if $user->id != '' && $user->id != 0} 
<div class="module administration quicklinks">
	<div class="hd">
	    Developer Quicklinks
	</div>
	<div class="bd">		
	    {permissions}
		    {if $permissions.manage == 1}
		    <a class="admin" href="{link controller=administration action=install_tables}">Install Tables</a>{br}
		    <a class="admin" href="{link controller=administration action=toggle_dev}">Toggle Dev</a>{br}
		    <a class="admin" href="{link controller=administration action=clear_smarty_cache}">Clear Smarty Cache Files</a>{br}
		    <a class="admin" href="{link controller=administration action=clear_css_cache}">Rebuild CSS</a>{br}
	        {/if}
	    {/permissions}
	</div>
</div>
{/if}
