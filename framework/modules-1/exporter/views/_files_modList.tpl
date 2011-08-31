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

<div class="exporter files-modlist">
	<div class="form_header">
		<h2>{'Export All Uploaded Files'|gettext}</h2>
	</div>
	<form method="post" action="">
		<input type="hidden" name="module" value="exporter" />
		<input type="hidden" name="action" value="page" />
		<input type="hidden" name="exporter" value="files" />
		<input type="hidden" name="page" value="export" />
		<table cellspacing="0" cellpadding="2" border="0">
			{if $user->isAdmin()}
			<tr>
				<td>
					<input type="checkbox" name="save_sample" value="1" class="checkbox">
					<b><label class="label ">Save as Sample Content for the '{$smarty.const.DISPLAY_THEME}' Theme?</label></b>
				</td>
			</tr>
			{/if}
			<tr>
				<td valign="top"><b>{'File Name Template:'|gettext}</b>
					<input type="text" name="filename" size="20" value="files" />
				</td>
			</tr>
				<td>
					<div style="border-top: 1px solid #CCCC;">{'Use __DOMAIN__ for this website\'s domain name and any strftime options for time specification. The extension will be added for you. Any other text will be preserved.'|gettext}<br /></div>
				</td>
			</tr>
			<tr>
				<td>
					<input class="awesome {$smarty.const.BTN_SIZE} {$smarty.const.BTN_COLOR}" type="submit" value="{'Export Files'|gettext}" />
				</td>
			</tr>
		</table>
	</form>
</div>