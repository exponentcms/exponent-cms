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

<div class="module events viewday">
	<div class="module-actions">
		<a class="weekviewlink" href="{link action=showall view=showall_Week time=$time}" title="{'View Entire Week'|gettext}">{'View Week'|gettext}</a>
        &#160;&#160;|&#160;&#160;
		<a class="monthviewlink" href="{link action=showall time=$time}" title="{'View Entire Month'|gettext}" alt="{'View Entire Month'|gettext}">{'View Month'|gettext}</a>
		{printer_friendly_link text='Printer-friendly'|gettext prepend='&#160;&#160;|&#160;&#160;'}
        {export_pdf_link prepend='&#160;&#160;|&#160;&#160;'}
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
	<p class="caption">
		<a class="module-actions" href="{link action=showall view=showall_Day time=$prev_timestamp3}" title="{$prev_timestamp3|format_date:"%A, %B %e, %Y"}">{$prev_timestamp3|format_date:"%a"}</a>&#160;&#160;&laquo;&#160;
		<a class="module-actions" href="{link action=showall view=showall_Day time=$prev_timestamp2}" title="{$prev_timestamp2|format_date:"%A, %B %e, %Y"}">{$prev_timestamp2|format_date:"%a"}</a>&#160;&#160;&laquo;&#160;
		<a class="module-actions" href="{link action=showall view=showall_Day time=$prev_timestamp}" title="{$prev_timestamp|format_date:"%A, %B %e, %Y"}">{$prev_timestamp|format_date:"%a"}</a>&#160;&#160;&laquo;&#160;&#160;&#160;&#160;&#160;
		<span>{$time|format_date:"%A, %B %e, %Y"}</span>&#160;&#160;&#160;&#160;&#160;&#160;&raquo;&#160;&#160;
		<a class="module-actions calendar_mngmntlink" href="{link action=showall view=showall_Day time=$next_timestamp}" title="{$next_timestamp|format_date:"%A, %B %e, %Y"}">{$next_timestamp|format_date:"%a"}</a>&#160;&#160;&raquo;&#160;
		<a class="module-actions calendar_mngmntlink" href="{link action=showall view=showall_Day time=$next_timestamp2}" title="{$next_timestamp2|format_date:"%A, %B %e, %Y"}">{$next_timestamp2|format_date:"%a"}</a>&#160;&#160;&raquo;&#160;
		<a class="module-actions calendar_mngmntlink" href="{link action=showall view=showall_Day time=$next_timestamp3}" title="{$next_timestamp3|format_date:"%A, %B %e, %Y"}">{$next_timestamp3|format_date:"%a"}</a>
        <a class="module-actions" style="float:right;" href="javascript:void(0);" id="J_popup_closeable">{'Go to Date'|gettext}</a>
	</p>
	<dl class="viewweek">
		{*{assign var=count value=0}*}
        {$count=0}
		{foreach from=$days.$time item=item}
			{*{assign var=count value=1}*}
            {$count=1}
			<dt>
				<span class="eventtitle">
                    <a class="itemtitle calendar_mngmntlink"
                        {if substr($item->location_data,1,8) != 'calevent'}
                            href="{if $item->location_data != 'event_registration'}{link action=show id=$item->date_id}{else}{link controller=eventregistration action=showByTitle title=$item->title}{/if}"
                        {/if}
                        ><strong>{$item->title}</strong>
                    </a>
                </span>
				{permissions}
                    {if substr($item->location_data,0,3) == 'O:8'}
                        <div class="item-actions">
                            {if $permissions.edit == 1}
                                {if $myloc != $item->location_data}
                                    {if $permissions.manage == 1}
                                        {icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                    {else}
                                        {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                    {/if}
                                {/if}
                                {icon action=edit record=$item date_id=$item->date_id title="Edit this Event"|gettext}
                            {/if}
                            {if $permissions.delete == 1}
                                {if $item->is_recurring == 0}
                                    {icon action=delete record=$item date_id=$item->date_id title="Delete this Event"|gettext}
                                {else}
                                    {icon action=delete_form class=delete record=$item date_id=$item->date_id title="Delete this Event"|gettext}
                                {/if}
                            {/if}
                            {if $permissions.manage == 1 || $permissions.edit == 1 || $permissions.delete == 1}
                                {br}
                            {/if}
                        </div>
                    {/if}
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
		fullpath: '{/literal}{$asset_path}js/calendar.js{literal}',
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
        var unixtime = parseInt(d / 1000);
        {/literal} {if ($smarty.const.SEF_URLS == 1)} {literal}
            window.location=eXp.PATH_RELATIVE+'event/showall/view/Day/time/'+unixtime+'/src/{/literal}{$__loc->src}{literal}';
        {/literal} {else} {literal}
            window.location=eXp.PATH_RELATIVE+'index.php?controller=event&action=showall&view=showall_Day&time='+unixtime+'&src={/literal}{$__loc->src}{literal}';
        {/literal} {/if} {literal}
	});

});

{/literal}
{/script}