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

{uniqueid prepend="cal" assign="name"}

{css unique="cal" link="`$smarty.const.PATH_RELATIVE`framework/modules-1/calendarmodule/assets/css/calendar.css"}

{/css}

{css unique="cal" link="`$smarty.const.PATH_RELATIVE`framework/modules-1/calendarmodule/assets/css/default.css"}

{/css}

<div class="module calendar monthly">
	<div class="module-actions">
		<a class="monthviewlink" href="{link action=viewmonth time=$time}">{'Calendar View'|gettext}</a>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <span class="listviewlink"></span>{'List View'|gettext}
		{permissions}
			{if $permissions.manage == 1}
				&nbsp;&nbsp;|&nbsp;&nbsp;
                <a class="adminviewlink mngmntlink" href="{link _common=1 view='Administration' action='show_view' time=$time}">{'Administration View'|gettext}</a>
			{/if}
		{/permissions}
        {printer_friendly_link text='Printer-friendly'|gettext prepend='&nbsp;&nbsp;|&nbsp;&nbsp;'}
        {export_pdf_link prepend='&nbsp;&nbsp;|&nbsp;&nbsp;'}
        {br}
	</div>
	<h1>
		{if $enable_ical == true}
			<a class="icallink module-actions" href="{link action=ical}" title="{'iCalendar Feed'|gettext}" alt="{'iCalendar Feed'|gettext}"> </a>
		{/if}
		{if $moduletitle}{$moduletitle}{/if}
	</h1>
	{permissions}
		<div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			{/if}
		</div>
	{/permissions}
	<p class="caption">
		&laquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewmonth view='Monthly List' time=$prev_timestamp3}" title="{$prev_timestamp3|format_date:"%B %Y"}">{$prev_timestamp3|format_date:"%b"}</a>&nbsp;&nbsp;&laquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewmonth view='Monthly List' time=$prev_timestamp2}" title="{$prev_timestamp2|format_date:"%B %Y"}">{$prev_timestamp2|format_date:"%b"}</a>&nbsp;&nbsp;&laquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewmonth view='Monthly List' time=$prev_timestamp}" title="{$prev_timestamp|format_date:"%B %Y"}">{$prev_timestamp|format_date:"%b"}</a>&nbsp;&nbsp;&laquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<strong>{$time|format_date:"%B %Y"}</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&raquo;&nbsp;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewmonth view='Monthly List' time=$next_timestamp}" title="{$next_timestamp|format_date:"%B %Y"}">{$next_timestamp|format_date:"%b"}</a>&nbsp;&nbsp;&raquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewmonth view='Monthly List' time=$next_timestamp2}" title="{$next_timestamp2|format_date:"%B %Y"}">{$next_timestamp2|format_date:"%b"}</a>&nbsp;&nbsp;&raquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewmonth view='Monthly List' time=$next_timestamp3}" title="{$next_timestamp3|format_date:"%B %Y"}">{$next_timestamp3|format_date:"%b"}</a>&nbsp;&nbsp;&raquo;
        <a class="module-actions" style="float:right;" href="javascript:void(0);" id="J_popup_closeable">{'Go to Date'|gettext}</a>
	</p>
	<dl class="viewweek">
		{foreach from=$days item=items key=ts}
			{if_elements array=$items}
				<dt>
					<div class="sectiontitle"><strong>
						<a class="itemtitle calendar_mngmntlink" href="{link action=viewday time=$ts}">{$ts|format_date:"%A, %b %e"}</a>
					</strong></div>
				</dt>
				<dd>
					{assign var=none value=1}
					{foreach from=$items item=item}
						{assign var=none value=0}
						<div class="paragraph">
							<a class="itemtitle calendar_mngmntlink" href="{link action=view id=$item->id date_id=$item->eventdate->id}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a>
							{permissions}
								<div class="item-actions">
									{if $permissions.edit == 1}
										{icon action=edit record=$item date_id=$item->eventdate->id title="Edit this Event"|gettext}
									{/if}
									{if $permissions.delete == 1}
										{if $item->is_recurring == 0}
											{icon action=delete record=$item date_id=$item->eventdate->id title="Delete this Event"|gettext}
										{else}
											{icon action=delete_form class=delete record=$item date_id=$item->eventdate->id title="Delete this Event"|gettext}
										{/if}
									{/if}
								</div>
							{/permissions}
							<div>
								{if $item->is_allday == 1}- {'All Day'|gettext}{else}
									{if $item->eventstart != $item->eventend}
										- {$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} {'to'|gettext} {$item->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
									{else}
										- {$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
									{/if}
								{/if}
								{br}
								{$item->summary}
							</div>
						</div>
					{/foreach}
				</dd>
				{if $none == 1}
					<div class="paragraph"><dd><strong>{'No Events.'|gettext}</strong></dd></div>
				{/if}
				{br}
			{/if_elements}
		{/foreach}
	</dl>
</div>

{script unique="cal-`$name`" yui3mods="node"}
{literal}

EXPONENT.YUI3_CONFIG.modules = {
	'gallery-calendar': {
		fullpath: '{/literal}{$smarty.const.PATH_RELATIVE}framework/modules-1/calendarmodule/assets/js/calendar.js{literal}',
		requires: ['node']
	}
}

YUI(EXPONENT.YUI3_CONFIG).use('gallery-calendar',function(Y){
    var today = new Date({/literal}{$time}{literal}*1000);

	//Popup
	new Y.Calendar('J_popup_closeable',{
		popup:true,
		closeable:true,
        startDay:{/literal}{$smarty.const.DISPLAY_START_OF_WEEK}{literal},
        date:today,
		action:['focus']
	}).on('select',function(d){
		//alert(d);
        var unixtime = parseInt(d / 1000);
        {/literal} {if ($smarty.const.SEF_URLS == 1)} {literal}
            window.location=eXp.PATH_RELATIVE+'calendarmodule/viewmonth/view/Monthly+List/time/'+unixtime+'/src/{/literal}{$__loc->src}{literal}';
        {/literal} {else} {literal}
            window.location=eXp.PATH_RELATIVE+'index.php?module=calendarmodule&action=viewmonth&view=Monthly+List&time='+unixtime+'&src={/literal}{$__loc->src}{literal}';
        {/literal} {/if} {literal}
	});

});

{/literal}
{/script}
