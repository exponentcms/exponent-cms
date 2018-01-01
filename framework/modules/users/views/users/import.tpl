{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
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

<div class="importer usercsv-starter">
	<div class="form_header">
        <div class="info-header">
            <h2>{'Import Users - CSV File Options'|gettext}</h2>
            <blockquote>{'Please enter the delimiter character of the csv file, the csv file to be uploaded, and the row within the csv file to start at. The start row is for files that have column headers, or if you just want to skip records in the csv file.'|gettext}</blockquote>
        </div>
	</div>
	<span style="color:red;">{$error}</span>
	{*{$form_html}*}
    <div>
        {form action=import_users_mapper}
            {control type="dropdown" name="delimiter" label="Delimiter Character"|gettext items=$delimiters}
            {control type=uploader name=upload label='CSV File to Upload'|gettext}
            {control type="checkbox" name="use_header" label='First Row is a Header?' value=1}
            {control type="text" name="rowstart" label="User Data begins in Row"|gettext value='1' size=6}
            {control type=buttongroup submit="Next"|gettext cancel="Cancel"|gettext}
        {/form}
    </div>
</div>
