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

<div class="module calendar viewweek"> 
	<div class="module-actions">
		<a class="monthviewlink" href="{link action=viewmonth time=$startweek}" title="{'View Entire Month'|gettext}">{'View Month'|gettext}</a>
        {printer_friendly_link text='Printer-friendly'|gettext prepend='&nbsp;&nbsp;|&nbsp;&nbsp;'}
        {export_pdf_link prepend='&nbsp;&nbsp;|&nbsp;&nbsp;'}
	</div>
	<h1>
        {if !empty($config->enable_ical)}
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
		<a class="module-actions calendar_mngmntlink" href="{link action=viewweek time=$startprevweek2}" title="{'Week of'|gettext} {$startprevweek2|format_date:"%B %e, %Y"}">{$startprevweek2|format_date:"%b %e"}</a>&nbsp;&nbsp;&laquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewweek time=$startprevweek}" title="{'Week of'|gettext} {$startprevweek|format_date:"%B %e, %Y"}">{$startprevweek|format_date:"%b %e"}</a>&nbsp;&nbsp;&laquo;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<span>{'Week of'|gettext} {$startweek|format_date:"%B %e, %Y"}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&raquo;&nbsp;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewweek time=$startnextweek}" title="{'Week of'|gettext} {$startnextweek|format_date:"%B %e, %Y"}">{$startnextweek|format_date:"%b %e"}</a>&nbsp;&nbsp;&raquo;&nbsp;
		<a class="module-actions calendar_mngmntlink" href="{link action=viewweek time=$startnextweek2}" title="{'Week of'|gettext} {$startnextweek2|format_date:"%B %e, %Y"}">{$startnextweek2|format_date:"%b %e"}</a>
        <a class="module-actions" style="float:right;" href="javascript:void(0);" id="J_popup_closeable">{'Go to Date'|gettext}</a>
	</p>
	<dl class="viewweek">

		{foreach from=$days item=items key=ts}
			<dt>
				<strong>
				{if $counts[$ts] != 0}
					<a class="itemtitle calendar_mngmntlink" href="{link action=viewday time=$ts}">{$ts|format_date:"%A, %b %e"}</a>
				{else}
					{$ts|format_date:"%A, %b %e"}
				{/if}
				</strong>
			</dt>
			{assign var=none value=1}
			{foreach from=$items item=item}
				{assign var=none value=0}
				<dd>
					<a class="itemtitle calendar_mngmntlink" href="{if $item->location_data != null}{link action=view id=$item->id date_id=$item->eventdate->id}{else}#{/if}" title="{$item->body|summarize:"html":"para"}">{$item->title}</a>
					{permissions}
                        {if $item->location_data != null}
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
                        {/if}
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
				</dd>
			{/foreach}
			{if $none == 1}
				<dd><em>{'No Events'|gettext}</em></dd>
			{/if}
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
    var today = new Date({/literal}{$startweek}{literal}*1000);

	//Popup
	new Y.Calendar('J_popup_closeable',{
		popup:true,
		closeable:true,
        startDay:{/literal}{$smarty.const.DISPLAY_START_OF_WEEK}{literal},
        date:today,
		action:['focus']
	}).on('select',function(d){
        var unixtime = parseInt(d / 1000);
        {/literal} {if ($smarty.const.SEF_URLS == 1)} {literal}
            window.location=eXp.PATH_RELATIVE+'calendarmodule/viewweek/time/'+unixtime+'/src/{/literal}{$__loc->src}{literal}';
        {/literal} {else} {literal}
            window.location=eXp.PATH_RELATIVE+'index.php?module=calendarmodule&action=viewweek&time='+unixtime+'&src={/literal}{$__loc->src}{literal}';
        {/literal} {/if} {literal}
	});

});

{/literal}
{/script}
