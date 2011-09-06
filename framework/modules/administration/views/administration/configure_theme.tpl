{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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
 
{css unique="theme-edit" link="`$smarty.const.PATH_RELATIVE`framework/core/assets/css/button.css"}

{/css}

<div class="theme configure">
	<div class="form_title">
		<h1>Configure {$name}</h1>
	</div>
	<div class="form_header">
		<p>Select the settings for the {$name}.</p>
	</div>
	{$form_html}
</div>

