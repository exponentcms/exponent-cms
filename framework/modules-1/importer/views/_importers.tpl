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

<div class="importer importers">
	<div class="form_header">
		<h2>{$_TR.form_title}</h2>
		<p>{$_TR.form_header}</p>
	</div>
	<table cellpadding="4" cellspacing="0" border="0" width="100%">
		{foreach from=$importers item=importer key=impname}
			<tr>
				<td class="administration_modmgrheader"><b>{$importer.name}</b> {$_TR.by|sprintf:$importer.author}</td>
			</tr>
			<tr>
				<td class="administration_modmgrbody">
					{$importer.description}
					<hr size='1'/>
					<a class="mngmntlink administration_mngmntlink" href="{link module=importer action=page page=start importer=$impname}">{$_TR.run}{$importer.name}</a>
				</td>
			</tr>
			<tr><td></td></tr>
		{foreachelse}
			<tr><td align="center"><i>{$_TR.no_importers}</i></td></tr>
		{/foreach}
	</table>
</div>