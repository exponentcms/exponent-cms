{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by James Hunt
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

{css unique="showlogin-logoutonly" link="`$smarty.const.PATH_RELATIVE`framework/core/assets/css/button.css"}

{/css}

{if $loggedin == true || $smarty.const.PREVIEW_READONLY == 1}
	<div class="login logout-only">
		<a class="awesome {$smarty.const.BTN_COLOR} {$smarty.const.BTN_SIZE}" href="{link action=logout}">{'Logout'|gettext}</a>
	</div>
{/if}	
