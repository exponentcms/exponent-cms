{*
 * Copyright (c) 2004-2014 OIC Group, Inc.
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

<div class="importer import_csv">
	<div class="form_header">
		<h2>{'Import Form Data - CSV Options'|gettext}</h2>
		<blockquote>{'Please enter the delimiter character of the csv file, the csv file to be uploaded, and the row within the csv file to start at. The start row is for files that have column headers, or if you just want to skip records in the csv file.'|gettext}</blockquote>
	</div>
	<span style="color:red;">{$error}</span>
	{*{$form_html}*}
    <div>
        {form action=import_csv_mapper}
            {control type="dropdown" name="delimiter" label="Delimiter Character"|gettext items=$delimiters}
            {control type=uploader name=upload label=gt('CSV File to Upload')}
            {control type="checkbox" name="use_header" label='First Row is a Header?' value=1}
            {control type="text" name="rowstart" label="Forms Data begins in Row"|gettext value='1' size=6}
            {control type="dropdown" name="forms_id" label="Target Form"|gettext items=$forms_list}
            {control type=buttongroup submit="Next"|gettext cancel="Cancel"|gettext}
        {/form}
    </div>
</div>
