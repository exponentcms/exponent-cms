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
<div class="container_editbox">

	<div class="container_editheader">
		{* I.E. requires a 'dummy' div inside of the above div, so that it
		   doesn't just 'lose' the margins and padding. jh 8/23/04 *}
		<div style="width: 100%">
		<table width="100%" cellpadding="0" cellspacing="3" border="0" class="container_editheader">
			<tr>
				<td valign="top" class="info">
					{$container->info.module}
					{if $container->view != ""}<br />{$_TR.shown_in_view|sprintf:$container->view}{/if}
				</td>
				<td align="right" valign="top">
					{if $container->info.clickable && $container->info.hasContent}
					{*<a class="mngmntlink container_mngmntlink" href="{$dest}&amp;ss={$container->info.source}&amp;sm={$container->info.class}">*}
					<a class="mngmntlink container_mngmntlink" href="{link action=content_selector module=$container->info.class src=$container->info.source channel_id=$dest}">
						<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE}add.png" title="{$_TR.select_content}" alt="{$_TR.select_content}" />
					</a>
					{/if}
				</td>
			</tr>
		</table>
		</div>
	</div>
	<div class="container_box">
		<div style="width: 100%">
		{$container->output}
		</div>
	</div>
</div>
<br /><br />