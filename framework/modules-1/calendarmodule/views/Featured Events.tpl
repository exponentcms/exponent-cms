{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
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
 
{css unique="cal" link="`$smarty.const.PATH_RELATIVE`framework/modules-1/calendarmodule/assets/css/calendar.css"}

{/css}

<div class="module calendar cal-admin">  
	<a href="{link _common=1 view=Default action=show_view}">Month View</a>{br}
	<h2>
		{if $enable_rss == true}
			<a href="{rsslink}"><img src="{$smarty.const.ICON_RELATIVE}rss-feed.gif" title="{'RSS Feed'|gettext}" alt="{'Link to RSS Feed'|gettext}" /></a>
		{/if}
		{if $moduletitle != ""}{$moduletitle}{/if}
	</h2>
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		{foreach from=$items item=item}
			{if $item->is_featured == 1}
				<tr>
					<td width="80" style="padding:3px 0px 3px 0px;">
						{if $item->image_path}
							<img src="{$item->image_path}" border="0" width="80" height="80"/>
						{else}
							&nbsp;
						{/if}
					</td>
					<td style="padding:3px 10px 3px 10px;">
						<table width=100% cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td>
									<a class="mngmntlink calendar_mngmntlink" href="{link action=view id=$item->id date_id=$item->eventdate->id}">{$item->title}</a>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									{$item->body|summarize:html:paralinks}
								</td>
							</tr>
							<tr>
								<td align="right">
									{permissions}
										<div class="item-actions">
											{if $permissions.edit == 1 || $item->permissions.edit == 1}
												{icon action=edit record=$item date_id=$item->eventdate->id title="Edit this Event"|gettext}
											{/if}
											{if $permissions.delete == 1 || $item->permissions.delete == 1}
												{if $item->is_recurring == 0}
													{icon action=delete record=$item date_id=$item->eventdate->id title="Delete this Event"|gettext}
												{else}
													{icon action=delete_form class=delete record=$item date_id=$item->eventdate->id title="Delete this Event"|gettext}
												{/if}
											{/if}
										</div>
									{/permissions}
								</td>
							</tr>
						</table>
					</td>
				</tr>
			{/if}
		{foreachelse}
			<table cellspacing="0" cellpadding="4" border="1" width="100%">
				<tr><td align="center"><i>{'No Events'|gettext}</i></td></tr>
			</table>
		{/foreach}
	</table>
	{permissions}
		{if $permissions.post == 1}
			<div class="module-actions">
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			</div>
		{/if}
		{br}
	{/permissions}
</div>