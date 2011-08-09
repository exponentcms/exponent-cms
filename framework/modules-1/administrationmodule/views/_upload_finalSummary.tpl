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

{css unique="install" corecss="tables"}

{/css}
<div class="exporter extension-finalsummary">
	<h1>New Extension Installation Summary</h1>
	{if $nofiles == 1}
		<h3>{$_TR.no_files}</h3>
	{else}
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="exp-skin-table">
			<thead>
				<tr>
					<th class="header administration_header">{$_TR.file}</th>
					<th class="header administration_header">{$_TR.status}</th>
				</tr>
			</thead>
			{foreach from=$success item=status key=file}
				<tr class="{cycle values="odd,even"}">
					<td>{$file}</td>
					<td>
						{if $status == 1}
							<span style="color: green">{$_TR.copied}</span>
						{else}
							<span style="color: red">{$_TR.failed}</span>
						{/if}
					</td>
				</tr>
			{/foreach}
		</table>
		<a class="awesome {$smarty.config.BTN_SIZE} {$smarty.const.BTN_COLOR}" href="{$redirect}">{$_TR.back}</a>
	{/if}
</div>