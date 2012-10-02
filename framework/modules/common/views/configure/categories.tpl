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

<div class="form_header">
	<div class="info-header">
		<div class="related-actions">
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("Categories Settings"|gettext) module="categories"}
		</div>
        <h2>{"Categories Settings"|gettext}</h2>
	</div>
</div>
<h2>{"Allow item grouping by category"|gettext}</h2>
{control type="checkbox" name="usecategories" label="Use Categories for this module?"|gettext value=1 checked=$config.usecategories}
{control type="checkbox" name="dontsort" label='Don\'t Sort List by Categories for this module?'|gettext value=1 checked=$config.dontsort}
{control type=text name=uncat label="Label for Un-Categorized items"|gettext value=$config.uncat|default:"Uncategorized"|gettext}
{*{chain module=expCat view=manage}*}
