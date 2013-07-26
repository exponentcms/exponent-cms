{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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

<div class="importer import_eql">
	<div class="form_header">
		<h2>{'Import Form Design'|gettext}</h2>
		<blockquote>{'This allows you import a form (in EQL format).'|gettext}</blockquote>
	</div>
    <div>
        {form action=import_eql_process}
            {control type="checkbox" name="include_data" label='Should stored form data also be included if available?' value=1 description='By default the form settings and controls are imported without including form data records'|gettext}
            {control type=uploader name=file accept=".eql" label=gt('Form EQL File')}
            {control class=uploadfile type=buttongroup submit="Import Form Design"|gettext cancel="Cancel"|gettext}
        {/form}
    </div>
</div>