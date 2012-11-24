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

{css unique="cal" link="`$asset_path`css/calendar.css"}

{/css}

{css unique="cal" link="`$asset_path`css/default.css"}

{/css}

<div class="module events default">
	<div class="module-actions">
		<span class="monthviewlink">{'Calendar View'|gettext}</span>
        &#160;&#160;|&#160;&#160;
        {icon class="listviewlink" action=showall view='showall_Monthly List' time=$time text='List View'|gettext}
		{permissions}
			{if $permissions.manage == 1}
				&#160;&#160;|&#160;&#160;
                {icon class="adminviewlink" action=showall view='showall_Administration' time=$time text='Administration View'|gettext}
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
        {br}
	</div>
	<h1>
        {ical_link}
        {if $moduletitle && !$config.hidemoduletitle}{$moduletitle}{/if}
	</h1>
    {if $config.moduledescription != ""}
        {$config.moduledescription}
    {/if}
    {$myloc=serialize($__loc)}
	{permissions}
		<div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			{/if}
		</div>
	{/permissions}
	<table id="calendar" summary="{$moduletitle|default:'Calendar'|gettext}">
        <div class="caption">
            &laquo;&#160;
            <a class="module-actions" href="{link action=showall time=$prevmonth3}" title="{$prevmonth3|format_date:"%B %Y"}">{$prevmonth3|format_date:"%b"}</a>&#160;&#160;&laquo;&#160;
            <a class="module-actions" href="{link action=showall time=$prevmonth2}" title="{$prevmonth2|format_date:"%B %Y"}">{$prevmonth2|format_date:"%b"}</a>&#160;&#160;&laquo;&#160;
            <a class="module-actions" href="{link action=showall time=$prevmonth}" title="{$prevmonth|format_date:"%B %Y"}">{$prevmonth|format_date:"%b"}</a>&#160;&#160;&laquo;&#160;&#160;&#160;&#160;&#160;
            <a class="module-actions" style="z-index:999;" href="javascript:void(0);" id="J_popup_closeable" title="{'Go to Date'|gettext}"><strong>{$time|format_date:"%B %Y"}</strong></a>&#160;&#160;&#160;&#160;&#160;&#160;&raquo;&#160;&#160;
            <a class="module-actions" href="{link action=showall time=$nextmonth}" title="{$nextmonth|format_date:"%B %Y"}">{$nextmonth|format_date:"%b"}</a>&#160;&#160;&raquo;&#160;
            <a class="module-actions" href="{link action=showall time=$nextmonth2}" title="{$nextmonth2|format_date:"%B %Y"}">{$nextmonth2|format_date:"%b"}</a>&#160;&#160;&raquo;&#160;
            <a class="module-actions" href="{link action=showall time=$nextmonth3}" title="{$nextmonth3|format_date:"%B %Y"}">{$nextmonth3|format_date:"%b"}</a>&#160;&#160;&raquo;
        </div>
		<tr class="daysoftheweek">
            {if $config.show_weeks}<th></th>{/if}
			{if $smarty.const.DISPLAY_START_OF_WEEK == 0}
			<th scope="col" abbr="{'Sun'|gettext}" title="'Sunday'|gettext}">{'Sunday'|gettext}</th>
			{/if}
			<th scope="col" abbr="{'Mon'|gettext}" title="{'Monday'|gettext}">{'Monday'|gettext}</th>
			<th scope="col" abbr="{'Tue'|gettext}" title="{'Tuesday'|gettext}">{'Tuesday'|gettext}</th>
			<th scope="col" abbr="{'Wed'|gettext}" title="{'Wednesday'|gettext}">{'Wednesday'|gettext}</th>
			<th scope="col" abbr="{'Thu'|gettext}" title="{'Thursday'|gettext}">{'Thursday'|gettext}</th>
			<th scope="col" abbr="{'Fri'|gettext}" title="{'Friday'|gettext}">{'Friday'|gettext}</th>
			<th scope="col" abbr="{'Sat'|gettext}" title="{'Saturday'|gettext}">{'Saturday'|gettext}</th>
			{if $smarty.const.DISPLAY_START_OF_WEEK != 0}
			<th scope="col" abbr="{'Sun'|gettext}" title="{'Sunday'|gettext}">{'Sunday'|gettext}</th>
			{/if}
		</tr>
        {$dayts=$now}
        {$dst=false}
		{foreach from=$monthly item=week key=weeknum}
            {*{$moredata=0}*}
			{*{foreach name=w from=$week key=day item=events}*}
                {*{$number=$counts[$weeknum][$day]}*}
                {*{if $number > -1}{$moredata=1}{/if}*}
			{*{/foreach}*}
			{*{if $moredata == 1}*}
                <tr class="week{if $currentweek == $weeknum} currentweek{/if}">
                    {if $config.show_weeks}
                        <td class="week{if $currentweek == $weeknum} currentweek{/if}">{$weeknum}</td>
                    {/if}
                    {foreach name=w from=$week key=day item=items}
                        {$number=$counts[$weeknum][$day]}
                        <td {if $dayts == $today}class="today"{elseif $number == -1}class="notinmonth"{else}class="oneday"{/if}>
                            {if $number > -1}
                                {if $number == 0}
                                    <span class="number{if $dayts == $today} today{/if}">
                                        {$day}
                                    </span>
                                {else}
                                    <a class="number" href="{link action=showall view=showall_Day time=$dayts}" title="{$dayts|format_date:'%A, %B %e, %Y'}">{$day}</a>
                                {/if}
                            {/if}
                            {foreach name=e from=$items item=item}
                                {if !empty($item->color)}
                                    {$class=" style=\"background:`$item->color`;color:`$item->color|contrast`\""}
                                {else}
                                    {$class=''}
                                {/if}
                                <div class="calevent{if $dayts == $today} today{/if}"{$class}>
                                    <a{if $config.usecategories && !empty($item->color)} class="{$item->color}"{/if}{$class}{if $config.show_allday && $item->is_allday == 1} style="border-color: {$item->color|brightness:+150};border-style: solid;padding-left: 2px;border-top: 0;border-bottom: 0;border-right: 0;"{/if}
                                    {if substr($item->location_data,1,8) != 'calevent'}
                                        href="{if $item->location_data != 'eventregistration'}{link action=show date_id=$item->date_id}{else}{link controller=eventregistration action=showByTitle title=$item->title}{/if}"
                                    {/if}
                                    title="{if $item->is_allday == 1}{'All Day'|gettext}{elseif $item->eventstart != $item->eventend}{$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} {'to'|gettext} {$item->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}{else}{$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}{/if} - {$item->body|summarize:"html":"para"}">{$item->title}</a>
                                    {permissions}
                                        {if substr($item->location_data,0,3) == 'O:8'}
                                        <div class="item-actions">
                                                {if $permissions.edit == 1}
                                                    {if $myloc != $item->location_data}
                                                        {if $permissions.manage == 1}
                                                            {icon img='arrow_merge.png' action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                                        {else}
                                                            {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                                        {/if}
                                                    {/if}
                                                    {icon img="edit.png" action=edit record=$item date_id=$item->date_id title="Edit this Event"|gettext}
                                                {/if}
                                                {if $permissions.delete == 1}
                                                    {if $item->is_recurring == 0}
                                                        {icon img="delete.png" action=delete record=$item date_id=$item->date_id title="Delete this Event"|gettext}
                                                    {else}
                                                        {icon img="delete.png" action=delete_recurring record=$item date_id=$item->date_id title="Delete this Event"|gettext}
                                                    {/if}
                                                {/if}
                                            </div>
                                        {/if}
                                    {/permissions}
                                </div>
                            {/foreach}
                            {if $number != -1}{$dayts=$dayts+86400}
                                {if !$dst}
                                    {if (date('I',$now) && !date('I',$dayts))}
                                        {$dayts=$dayts+3600}
                                        {$dst=true}
                                    {elseif (!date('I',$now) && date('I',$dayts))}
                                        {$dayts=$dayts-3600}
                                        {$dst=true}
                                    {/if}
                                {/if}
                            {/if}
                        </td>
                    {/foreach}
                </tr>
			{*{/if}*}
		{/foreach}
	</table>
</div>

{script unique="cal-`$name`" yui3mods="node"}
{literal}

EXPONENT.YUI3_CONFIG.modules = {
	'gallery-calendar': {
		fullpath: '{/literal}{$asset_path}js/calendar.js{literal}',
		requires: ['node']
	}
}

YUI(EXPONENT.YUI3_CONFIG).use('gallery-calendar',function(Y){
	var today = new Date({/literal}{$time}{literal}*1000);

	//Popup
	var cal = new Y.Calendar('J_popup_closeable',{
		popup:true,
		closeable:true,
		startDay:{/literal}{$smarty.const.DISPLAY_START_OF_WEEK}{literal},
		date:today,
		action:['click'],
//        useShim:true
	}).on('select',function(d){
		var unixtime = parseInt(d / 1000);
    {/literal} {if ($smarty.const.SEF_URLS == 1)} {literal}
        window.location=eXp.PATH_RELATIVE+'event/showall/time/'+unixtime+'/src/{/literal}{$__loc->src}{literal}';
    {/literal} {else} {literal}
        window.location=eXp.PATH_RELATIVE+'index.php?controller=event&action=showall&time='+unixtime+'&src={/literal}{$__loc->src}{literal}';
    {/literal} {/if} {literal}
	});
    Y.one('#J_popup_closeable').on('click',function(d){
        cal.show();
    });

});

{/literal}
{/script}
