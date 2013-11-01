{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
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
		{icon class="monthviewlink" action=showall time=$time text='Calendar View'|gettext}
        &#160;&#160;|&#160;&#160;
        {icon class="listviewlink" action=showall view='showall_Monthly List' time=$time text='List View'|gettext}
		{permissions}
			{if $permissions.manage}
				&#160;&#160;|&#160;&#160;
                {icon class="adminviewlink" action=showall view=showall_Administration time=$time text='Administration View'|gettext}
                {if !$config.disabletags}
                    &#160;&#160;|&#160;&#160;
                    {icon controller=expTag class="manage" action=manage_module model='event' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
                    &#160;&#160;|&#160;&#160;
                    {icon controller=expCat action=manage model='event' text="Manage Categories"|gettext}
                {/if}
			{/if}
        {/permissions}
        {printer_friendly_link text='Printer-friendly'|gettext prepend='&#160;&#160;|&#160;&#160;'}
        {export_pdf_link prepend='&#160;&#160;|&#160;&#160;'}
        {permissions}
            &#160;&#160;|&#160;&#160;
			{*<span class="listviewlink">{'Past Events View'|gettext}</span>*}
            {icon class="listviewlink" text='Past Events View'|gettext}
			{if $permissions.manage}
				&#160;&#160;|&#160;&#160;
				{icon class=delete action=delete_all_past onclick="return confirm('"|cat:("Delete All Past Events?"|gettext)|cat:"');" title="Delete All Past Events"|gettext text="Purge All Past Events"|gettext}
				{br}
			{/if}
		{/permissions}
	</div>
	<h1>
        {ical_link}
        {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle} - {'Past Events View'|gettext}{/if}

	</h1>
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {$myloc=serialize($__loc)}
	{permissions}
		<div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			{/if}
		</div>
	{/permissions}
	<table cellspacing="0" cellpadding="4" border="0" width="100%" class="exp-skin-table">
		<thead>
			<tr>
				<th class="header calendarcontentheader">{'Event Title'|gettext}</th>
				<th class="header calendarcontentheader">{'When'|gettext}</th>
				<th class="header calendarcontentheader">{nbsp}</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$items item=item}
			<tr class="{cycle values="odd,even"}">
				<td><a class="itemtitle{if $item->is_cancelled} cancelled{/if}{if $config.usecategories && !empty($item->color)} {$item->color}{/if}" href="{link action=show date_id=$item->date_id}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a></td>
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
							{if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                                {if $myloc != $item->location_data}
                                    {if $permissions.manage}
                                        {icon img='arrow_merge.png' action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                    {else}
                                        {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                    {/if}
                                {/if}
								{icon img='edit.png' action=edit record=$item date_id=$item->date_id title="Edit this Event"|gettext}
                                {icon img='copy.png' action=copy record=$item date_id=$item->date_id title="Copy this Event"|gettext}
							{/if}
							{if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
								{if $item->is_recurring == 0}
									{icon img='delete.png' action=delete record=$item date_id=$item->date_id title="Delete this Event"|gettext}
								{else}
									{icon img='delete.png' action=delete_recurring record=$item date_id=$item->date_id title="Delete this Event"|gettext}
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
