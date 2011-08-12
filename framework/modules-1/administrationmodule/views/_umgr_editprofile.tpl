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
<div class="administrationmodule edit-profile">
	<div class="form_header">
        	<h1>{if $is_edit}{'Edit User Account'|gettext}{else}{'New User Account'|gettext}{/if}</h1>
	        <p>
			{if $is_edit}
				{'Use this form to modify a user\'s profile.'|gettext}
			{else}
				{'Use this form to create a new user.'|gettext}
			{/if}
		</p>
	</div>
	{$form_html}
</div>
