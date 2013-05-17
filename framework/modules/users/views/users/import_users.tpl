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

<div class="importer usercsv-starter">
	<div class="form_header">
		<h2>{'Import Users - CSV File Options'|gettext}</h2>
		<blockquote>{'Please enter the delimiter character of the csv file, the csv file to be uploaded, and the row within the csv file to start at. The start row is for files that have column headers, or if you just want to skip records in the csv file.'|gettext}</blockquote>
	</div>
	<span style="color:red;">{$error}</span>
	{$form_html}
</div>
