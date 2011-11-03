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

{css unique="install-buttons" link="`$smarty.const.PATH_RELATIVE`framework/core/assets/css/button.css"}

{/css}

{css unique="install" corecss="tables"}

{/css}
<div class="exporter extension-finalsummary">
	<h1>New Extension Installation Summary</h1>
	{if $nofiles == 1}
		<h3>{'No files to copy.  If you hit refresh, this is normal.'|gettext}</h3>
	{else}
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="exp-skin-table">
			<thead>
				<tr>
					<th class="header administration_header">{'File'|gettext}</th>
					<th class="header administration_header">{'Status'|gettext}</th>
				</tr>
			</thead>
			{foreach from=$success item=status key=file}
				<tr class="{cycle values="odd,even"}">
					<td>{$file}</td>
					<td>
						{if $status == 1}
							<span style="color: green">{'Copied'|gettext}</span>
						{else}
							<span style="color: red">{'Failed'|gettext}</span>
						{/if}
					</td>
				</tr>
			{/foreach}
		</table>
		<a class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{$redirect}">{'Back'|gettext}</a>
	{/if}
</div>