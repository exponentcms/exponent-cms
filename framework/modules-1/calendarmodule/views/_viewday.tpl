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

<div class="module calendar viewday"> 
	<div class="module-actions">
		<a class="weekviewlink" href="{link action=viewweek time=$now view=_viewweek}" title="{'View Entire Week'|gettext}">{'View Week'|gettext}</a>
        &nbsp;&nbsp;|&nbsp;&nbsp;
		<a class="monthviewlink" href="{link action=viewmonth time=$item->eventstart}" title="{'View Entire Month'|gettext}" alt="{'View Entire Month'|gettext}">{'View Month'|gettext}</a>
		{printer_friendly_link text='Printer-friendly'|gettext prepend='&nbsp;&nbsp;|&nbsp;&nbsp;'}
        {export_pdf_link prepend='&nbsp;&nbsp;|&nbsp;&nbsp;'}
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
		<a class="module-actions calendar_mngmntlink" href="{link action=viewday time=$prevday3}" title="{$prevday3|format_date:"%A, %B %e, %Y"}">{$prevday3|format_date:"%a"}</a>&nbsp;&nbsp;&laquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewday time=$prevday2}" title="{$prevday2|format_date:"%A, %B %e, %Y"}">{$prevday2|format_date:"%a"}</a>&nbsp;&nbsp;&laquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewday time=$prevday}" title="{$prevday|format_date:"%A, %B %e, %Y"}">{$prevday|format_date:"%a"}</a>&nbsp;&nbsp;&laquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<span>{$now|format_date:"%A, %B %e, %Y"}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&raquo;&nbsp;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewday time=$nextday}" title="{$nextday|format_date:"%A, %B %e, %Y"}">{$nextday|format_date:"%a"}</a>&nbsp;&nbsp;&raquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewday time=$nextday2}" title="{$nextday2|format_date:"%A, %B %e, %Y"}">{$nextday2|format_date:"%a"}</a>&nbsp;&nbsp;&raquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewday time=$nextday3}" title="{$nextday3|format_date:"%A, %B %e, %Y"}">{$nextday3|format_date:"%a"}</a>
        <a class="module-actions" style="float:right;" href="javascript:void(0);" id="J_popup_closeable">{'Go to Date'|gettext}</a>
	</p>
	<dl class="viewweek">
		{assign var=count value=0}
		{foreach from=$events item=item}
			{assign var=count value=1}
			<dt>
				<span class="eventtitle"><a class="itemtitle calendar_mngmntlink" href="{link action=view id=$item->id date_id=$item->eventdate->id}"><strong>{$item->title}</strong></a></span>
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
						{if $permissions.manage == 1 || $permissions.edit == 1 || $permissions.delete == 1}
							{br}
						{/if}
					</div>
				{/permissions}
			</dt>
			<dd>
				<p>
					<span><strong>
						{if $item->is_allday == 1}{'All Day'|gettext}{else}
							{if $item->eventstart != $item->eventend}
								{$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} {'to'|gettext} {$item->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
							{else}
								{$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
							{/if}
						{/if}
					</strong></span>
					{br}
					{$item->body|summarize:"html":"paralinks"}
				</p>
			</dd>
		{/foreach}
		{if $count == 0}
			<dd><em>{'No Events'|gettext}</em></dd>
		{/if}
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
    var today = new Date({/literal}{$now}{literal}*1000);

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
            window.location=eXp.URL_FULL+'calendarmodule/viewday/time/'+unixtime+'/src/{/literal}{$__loc->src}{literal}';
        {/literal} {else} {literal}
            window.location=eXp.URL_FULL+'index.php?module=calendarmodule&action=viewday&time='+unixtime+'&src={/literal}{$__loc->src}{literal}';
        {/literal} {/if} {literal}
	});

});

{/literal}
{/script}