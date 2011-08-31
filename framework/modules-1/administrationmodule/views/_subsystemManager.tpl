{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by James Hunt
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
<div class="form_title">{'Manage Subsystems'|gettext}</div>
<div class="form_header">{'This page lists all installed subsystems that Exponent recognizes<br /><br />Clicking the "View Files" link will bring up a list of files that belog to the subsystem, along with file integrity checksums.'|gettext}
<br /><br />
<a class="mngmntlink administration_mngmntlink" href="{link action=upload_extension}">{'Upload New Subsystem'|gettext}</a></div>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
{foreach from=$info key=subsys item=meta}
	<tr>
		<td style="background-color: lightgrey"><b>{$meta.name}</b> {'by'|gettext} {$meta.author} {'version'|gettext}{$meta.version}</td>
		<td style="background-color: lightgrey" align="right"><b>{$subsys}</td>
	</tr>
	<tr>
		<td colspan="3" style="padding-left: 10px; border: 1px solid lightgrey;">
{*			<a class="mngmntlink administration_mngmntlink" href="{link module=info action=showfiles type=$smarty.const.CORE_EXT_SUBSYSTEM name=$subsys}">*}
				{'View Files'|gettext}
			</a>
			<hr size="1" />
			{$meta.description}
		</td>
	</tr>
	<tr><td></td></tr>
{/foreach}
</table>