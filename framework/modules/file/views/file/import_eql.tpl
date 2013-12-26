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

<div class="importer eql-restoreform">
	<div class="form_header">
		<h2>{'Restore Database from Archive'|gettext}</h2>
		<blockquote>{'This allows you upload/import a database backup (in EQL format) to the server.  Doing so will restore the database to the state saved in that backup file.'|gettext}</blockquote>
        <p style="color: red"><strong>{'Continuing will delete ALL existing data in any restored tables!'|gettext}</strong></p>
	</div>
    <div>
        {form action=import_eql_process}
            {control type=uploader name=file accept=".eql" label=gt('EQL File')}
            {control class=uploadfile type=buttongroup submit="Restore"|gettext}
        {/form}
    </div>

</div>