{*
 * Copyright (c) 2004-2016 OIC Group, Inc.
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
        {if !$config.disable_links}
            {icon class="monthviewlink" action=showall time=$time text='Calendar View'|gettext}
			{if !bs()}
				{nbsp count=2}|{nbsp count=2}
		    {/if}
            {icon class="listviewlink" action=showall view='showall_Monthly List' time=$time text='List View'|gettext}
        {/if}
        {permissions}
            <div class="module-actions">
				{if !bs()}
					{nbsp count=2}|{nbsp count=2}
			    {/if}
                {*<span class="adminviewlink">{'Administration View'|gettext}</span>*}
                {icon class="adminviewlink" text='Administration View'|gettext}
                {if !$config.disabletags}
					{if !bs()}
						{nbsp count=2}|{nbsp count=2}
				    {/if}
                    {icon controller=expTag class="manage" action=manage_module model='event' text="Manage Tags"|gettext}
                {/if}
                {if $config.usecategories}
					{if !bs()}
						{nbsp count=2}|{nbsp count=2}
				    {/if}
                    {icon controller=expCat action=manage model='event' text="Manage Categories"|gettext}
                {/if}
            </div>
        {/permissions}
		{printer_friendly_link text='Printer-friendly'|gettext prepend='&#160;&#160;|&#160;&#160;'|not_bs}
        {export_pdf_link prepend='&#160;&#160;|&#160;&#160;'|not_bs}
		{if !bs()}
			{nbsp count=2}|{nbsp count=2}
	    {/if}
		{icon class="listviewlink" action=showall view='showall_Past Events' time=$time text='Past Events View'|gettext}{br}
	</div>
	<{$config.heading_level|default:'h1'}>
        {ical_link}
        {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle} - {'Administration View'|gettext}{/if}
	</{$config.heading_level|default:'h1'}>
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {$myloc=serialize($__loc)}
	{permissions}
		<div class="module-actions">
			{if $permissions.create}
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			{/if}
			{control type=text name="eventsearchinput" label='Limit items to those including:'|gettext}
		</div>
	{/permissions}
	<table cellspacing="0" cellpadding="4" border="0" width="100%" class="exp-skin-table">
		<thead>
			<tr>
				<strong><em>
				<th class="header calendarcontentheader">{'Event Title'|gettext}</th>
				<th class="header calendarcontentheader">{'When'|gettext}</th>
				<th class="header calendarcontentheader">&#160;</th>
				</em></strong>
			 </tr>
		</thead>
		<tbody>
		{foreach from=$items item=item}
			<tr class="item {cycle values="odd,even"}">
				<td><a class="itemtitle{if $item->is_cancelled} cancelled{/if}{if !empty($item->color)} {$item->color}{/if}" href="{link action=show date_id=$item->date_id}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a></td>
				<td class="itemdate">
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
			<tr><td colspan="2" align="center"><em>{'No Events'|gettext}</em></td></tr>
		{/foreach}
		</tbody>
	</table>
</div>

{script unique="`$name`search" jquery='jquery.searcher'}
{literal}
    $(".events.cal-admin").searcher({
        itemSelector: ".item",
        textSelector: ".itemtitle, td.itemdate",
        inputSelector: "#eventsearchinput",
		toggle: function(item, containsText) {
			 // use a typically jQuery effect instead of simply showing/hiding the item element
			 if (containsText)
				 $(item).fadeIn();
			 else
				 $(item).fadeOut();
		}
    });
{/literal}
{/script}
