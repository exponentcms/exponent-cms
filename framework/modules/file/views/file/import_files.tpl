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

<div class="importer importers">
	<div class="form_header">
		<h2>{'Restore Uploaded Files from Archive'|gettext}</h2>
		<blockquote>{'To restore your uploaded files, simply select and upload the files archive.'|gettext}</blockquote>
	</div>
    <div>
        {form action=import_files_process}
            {control type=uploader name=file accept="application/x-gzip" label=gt('Files Archive')}
            {control class=uploadfile type=buttongroup submit="Restore"|gettext}
        {/form}
    </div>
</div>