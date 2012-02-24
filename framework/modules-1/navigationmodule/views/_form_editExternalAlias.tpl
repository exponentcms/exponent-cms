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

<div class="navigationmodule form-editExternalAliasPage">
    <div class="info-header">
        <div class="related-actions">
			{help text="Get Help"|gettext|cat:" "|cat:("Editing External Alias Pages"|gettext) module="edit-external-page"}
        </div>
	    <h1>{if $is_edit == 1}{'Edit Existing External Alias'|gettext}{else}{'New External Alias'|gettext}{/if}</h1>
	</div>
	<div class="form_header">
		{'Below, enter the web address you want this section to link to.'|gettext}
	</div>
	{$form_html}
</div>