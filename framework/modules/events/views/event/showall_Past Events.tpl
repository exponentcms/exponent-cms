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
 
{css unique="cal" link="`$asset_path`css/calendar.css" corecss="tables"}

{/css}

<div class="module events cal-admin">
	<div class="module-actions">
		<a class="monthviewlink" href="{link action=showall time=$time}">{'Calendar View'|gettext}</a>
        &#160;&#160;|&#160;&#160;
        <a class="listviewlink" href="{link action=showall view='showall_Monthly List' time=$time}">{'List View'|gettext}</a>
		{permissions}
			{if $permissions.manage == 1}
				&#160;&#160;|&#160;&#160;
                <a class="adminviewlink mngmntlink" href="{link action=showall view=showall_Administration time=$time}">{'Administration View'|gettext}</a>
			{/if}
        {/permissions}
        {printer_friendly_link text='Printer-friendly'|gettext prepend='&#160;&#160;|&#160;&#160;'}
        {export_pdf_link prepend='&#160;&#160;|&#160;&#160;'}
        {br}
        {permissions}
			<span class="listviewlink">{'Past Events View'|gettext}</span>
			{if $permissions.manage == 1}
				&#160;&#160;|&#160;&#160;
				<img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'delete.png'}" title="{'Delete All Past Events'|gettext}" alt="{'Delete All Past Events'|gettext}" />
				<a class="mngmntlink" href="{link action=delete_all_past}" onclick="return confirm('{'Delete All Past Events?'|gettext}');" title="{'Delete All Past Events'|gettext}">{'Purge All Past Events'|gettext}</a>
				{br}
			{/if}
		{/permissions}
	</div>
	<h1>
        {ical_link}
        {if $moduletitle && !$config->hidemoduletitle}{$moduletitle} - {'Past Events View'|gettext}{/if}

	</h1>
    {if $config->moduledescription != ""}
        {$config->moduledescription}
    {/if}
    {$myloc=serialize($__loc)}
	{permissions}
		<div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			{/if}
		</div>
	{/permissions}
	<table cellspacing="0" cellpadding="4" border="0" width="100%" class="exp-skin-table">
		<thead>
			<tr>
				<th class="header calendarcontentheader">{'Event Title'|gettext}</th>
				<th class="header calendarcontentheader">{'When'|gettext}</th>
				<th class="header calendarcontentheader">&#160;</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$items item=item}
			<tr class="{cycle values="odd,even"}">
				<td><a class="itemtitle calendar_mngmntlink" href="{link action=show id=$item->date_id}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a></td>
				<td>
					{if $item->is_allday == 1}
						{$item->eventstart|format_date}
					{else}
						{if $event->eventstart != $event->eventend}
							{$item->eventstart|format_date:"%b %e %Y"} @ {$item->eventstart|format_date:"%l:%M %p"} - {$event->eventend|format_date:"%l:%M %p"}
						{else}
							{$item->eventstart|format_date:"%b %e %Y"} @ {$item->eventstart|format_date:"%l:%M %p"}
						{/if}		
					{/if}
				</td>
				<td>
					{permissions}
						<div class="item-actions">
							{if $permissions.edit == 1}
                                {if $myloc != $item->location_data}
                                    {if $permissions.manage == 1}
                                        {icon img='arrow_merge.png' action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                    {else}
                                        {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                    {/if}
                                {/if}
								{icon img='edit.png' action=edit record=$item date_id=$item->date_id title="Edit this Event"|gettext}
							{/if}
							{if $permissions.delete == 1}
								{if $item->is_recurring == 0}
									{icon img='delete.png' action=delete record=$item date_id=$item->date_id title="Delete this Event"|gettext}
								{else}
									{icon img='delete.png' action=delete_form record=$item date_id=$item->date_id title="Delete this Event"|gettext}
								{/if}
							{/if}
						</div>
					{/permissions}
				</td>
			</tr>
		{foreachelse}
			<tr><td colspan="2" align="center"><em>{'No past events.'|gettext}</em></td></tr>
		{/foreach}
		</tbody>
	</table>
</div>
