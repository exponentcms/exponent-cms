{*
 * Copyright (c) 2004-2006 OIC Group, Inc.
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
        	<h1>{if $is_edit}{$_TR.form_title_edit}{else}{$_TR.form_title_new}{/if}</h1>
	        <p>
			{if $is_edit}
				{$_TR.form_header_edit}
			{else}
				{$_TR.form_header_new}
			{/if}
		</p>
	</div>
	{$form_html}
</div>
