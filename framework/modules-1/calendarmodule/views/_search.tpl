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
 
{css unique="cal" link="`$smarty.const.PATH_RELATIVE`framework/modules-1/calendarmodule/assets/css/calendar.css"}

{/css}

<div class="module calendar search"> 
	<b>{$title}</b><br/>
	<table cellpadding="0" valign="top" cellspacing="0" width="100%" border="0">
		{foreach from=$days item=event}
			<tr><td><strong>{$event->eventdate|format_date:"%A, %B %e, %Y"}</strong><hr size="1" /></td></tr>
			{foreach from=$event->dates item=dates}
				<tr><td style="padding-left: 15px">
					{if $permissions.edit == 1 || $event->permissions.edit == 1}
						{if $event->approved == 1}
							<a href="{link action=edit id=$event->id date_id=$event->eventdate->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'edit.png'}" title="{'Edit'|gettext}" alt="{'Edit'|gettext}" /></a>&nbsp;
						{else}
							<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'edit.disabled.png'}" title="{''|gettext}" alt="{''|gettext}" />
						{/if}
					{/if}
					{if $permissions.delete == 1 || $event->permissions.delete == 1}
						{if $event->approved == 1}
							{if $event->is_recurring == 0}
								<a href="{link action=delete id=$event->id}" onclick="return confirm('{'Confirm you want to delete this item'|gettext}');"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'delete.png'}" title="{'Delete'|gettext}" alt="{'Delete'|gettext}" /></a>
							{else}
								<a href="{link action=delete_form date_id=$event->eventdate->id id=$event->id}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'delete.png'}" title="{'Delete'|gettext}" alt="{'Delete'|gettext}" /></a>
							{/if}
						{else}
						<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'delete.disabled.png'}" title="{''}" alt="{''|gettext}" />
						{/if}
					{/if}
					<div style="padding-left: 10px">
						<b>
						{if $event->is_allday == 1}{'All Day'|gettext}{else}
							{$dates->eventstart|format_date:"%l:%M %P"} - {$dates->eventend|format_date:"%l:%M %P"}
						{/if}</b><br/>
						{$event->body|summarize:"html":"paralinks"}</br>
						&nbsp;&nbsp;<a class="mngmntlink calendar_mngmntlink" href="{link action=view id=$event->id date_id=$dates->id}">{'Click here for more information or to register.'|gettext}</a>
					</div><br/>
				</td></tr>
			{/foreach}
		{/foreach}
		{if $count == 0}
			<tr><td><i>{'No Events'|gettext}</i></td></tr>
		{/if}
	</table>
</div>
