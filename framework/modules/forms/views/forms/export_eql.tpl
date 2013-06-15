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

<div class="exporter export-eql">
	<div class="form_header">
		<h2>{'Export Form Design - EQL Options'|gettext}</h2>
	</div>
    <div>
        {form action=export_eql_process}
            {control type=hidden name=id value=$id}
            {control type="checkbox" name="include_data" label='Should stored form data also be included?' value=1 description='By default the form settings and controls are saved without including form data records'|gettext}
            {control type=buttongroup submit="Export Form Design"|gettext cancel="Cancel"|gettext}
        {/form}
    </div>
</div>