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

{css unique="exporters-buttons" link="`$smarty.const.PATH_RELATIVE`framework/core/assets/css/button.css"}

{/css}

<div class="exporter exporters">
	<div class="form_header">
		<h2>{'Data Exporters'|gettext}</h2>
		<p>{'This page lists all installed exporters that Exponent recognizes and gives some information about each.'|gettext}</p>
	</DIV>
	<table cellpadding="4" cellspacing="0" border="0" width="100%">
		{foreach from=$exporters item=exporter key=impname}
			<tr>
				<td class="administration_modmgrheader"><b>{$exporter.name}</b> {'by'|gettext|cat:' %s'|sprintf:$exporter.author}</td>
			</tr>
			<tr>
				<td class="administration_modmgrbody">
					{$exporter.description}
					<hr size='1'/>
					<a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{link module=exporter action=page page=start exporter=$impname}">{'Run'|gettext} {$exporter.name}</a>
				</td>
			</tr>
			<tr><td></td></tr>
		{foreachelse}
			<tr><td align="center"><i>{'No exporters are installed.'|gettext}</i></td></tr>
		{/foreach}
	</table>
</div>