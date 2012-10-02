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
		    {help text="Get Help"|gettext|cat:" "|cat:("with"|gettext)|cat:" "|cat:("Module Title Settings"|gettext) module="module-title"}
		</div>
        <h2>{"Module Title Settings"|gettext}</h2>
	</div>
</div>
{control type="checkbox" name="hidemoduletitle" label="Hide Module Title?"|gettext value=1 checked=$config.hidemoduletitle}
{control type="html" name="moduledescription" label="Module Description"|gettext value=$config.moduledescription}
