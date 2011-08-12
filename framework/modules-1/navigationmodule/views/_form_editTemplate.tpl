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
<div class="navigationmodule edit-template">
	<div class="form_header">
		<h1>{'Pageset Properties'|gettext}</h1>
              	<p>{'Pageset Properties are mapped onto section properties when a Pageset is selected as the Page Type.'|gettext}</p>
	</div>
	{if $is_top == 1}
	{br}{br}
	{'The name you specify for this Pageset will be used for reference only.  It will be replaced by whatever is entered into the name field of the "Add Section" form.'|gettext}
	{/if}
	{$form_html}
</div>
