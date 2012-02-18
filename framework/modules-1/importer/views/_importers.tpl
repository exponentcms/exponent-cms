{*
 * Copyright (c) 2004-2012 OIC Group, Inc.
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

{css unique="importers-buttons" corecss="button"}

{/css}

<div class="importer importers">
	<div class="form_header">
		<h2>{'Data Importers'|gettext}</h2>
		<p>{'This page lists all installed importers that Exponent recognizes and gives some information about each'|gettext}</p>
	</div>
	<table cellpadding="4" cellspacing="0" border="0" width="100%">
		{foreach from=$importers item=importer key=impname}
			<tr>
				<td class="administration_modmgrheader"><b>{$importer.name}</b> {'by'|gettext|cat:' %s'|sprintf:$importer.author}</td>
			</tr>
			<tr>
				<td class="administration_modmgrbody">
					{$importer.description}
					<hr size='1'/>
					<a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{link module=importer action=page page=start importer=$impname}">{'Run'|gettext} {$importer.name}</a>
				</td>
			</tr>
			<tr><td></td></tr>
		{foreachelse}
			<tr><td align="center"><i>{'No importers are installed.'|gettext}</i></td></tr>
		{/foreach}
	</table>
</div>