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

<div class="importer usercsv-process">
	<div class="form_header">
		<h2>{'Import Users - Import Options'|gettext}</h2>
		<blockquote>{'Select the methods you would like to use to generate usernames and passwords.&#160;&#160;If you supply a default password, it will be used for all the users imported.  If you mapped username and password as a field in the mapping screen, you won\'t need to do anything here.'|gettext}</blockquote>
	</div>
	{*{$form_html}*}
    <div>
        {form action=import_users_display}
            {control type="hidden" name="column" value=$params.column}
            {control type="hidden" name="delimiter" value=$params.delimiter}
            {control type="hidden" name="use_header" value=$params.use_header}
            {control type="hidden" name="filename" value=$params.filename}
            {control type="hidden" name="rowstart" value=$params.rowstart}
            {control type="dropdown" name="unameOptions" label="User Name Generations Options"|gettext items=$uname_options selected="INFILE"}
            {control type="dropdown" name="pwordOptions" label="Password Generation Options"|gettext items=$pword_options selected="defpass"}
            {if !$pword_disabled}
                {control type="text" name="pwordText" label="Default Password"|gettext size=10}
            {/if}
            {control type="checkbox" name="update" label='Update users already in database, instead of creating new user?' value=1}
            {control type=buttongroup submit="Next"|gettext cancel="Cancel"|gettext}
        {/form}
    </div>
</div>
