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

<div class="module events viewweek">
	<div class="module-actions">
		<a class="monthviewlink" href="{link action=showall time=$time}" title="{'View Entire Month'|gettext}">{'View Month'|gettext}</a>
        {printer_friendly_link text='Printer-friendly'|gettext prepend='&#160;&#160;|&#160;&#160;'}
        {export_pdf_link prepend='&#160;&#160;|&#160;&#160;'}
	</div>
	<h1>
        {ical_link}
        {if $moduletitle && !$config->hidemoduletitle}{$moduletitle}{/if}
	</h1>
    {if $config->moduledescription != ""}
        {$config->moduledescription}
    {/if}
	{permissions}
		<div class="module-actions">
			{if $permissions.create == 1}
				{icon class=add action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			{/if}
		</div>
	{/permissions}
	<p class="caption">
		<a class="module-actions calendar_mngmntlink" href="{link action=showall view=showall_Week time=$prev_timestamp2}" title="{'Week of'|gettext} {$prev_timestamp2|format_date:"%B %e, %Y"}">{$prev_timestamp2|format_date:"%b %e"}</a>&#160;&#160;&laquo;&#160;
		<a class="module-actions calendar_mngmntlink" href="{link action=showall view=showall_Week time=$prev_timestamp}" title="{'Week of'|gettext} {$prev_timestamp|format_date:"%B %e, %Y"}">{$prev_timestamp|format_date:"%b %e"}</a>&#160;&#160;&laquo;&#160;&#160;&#160;&#160;&#160;
		<span>{'Week of'|gettext} {$time|format_date:"%B %e, %Y"}</span>&#160;&#160;&#160;&#160;&#160;&#160;&raquo;&#160;&#160;
		<a class="module-actions calendar_mngmntlink" href="{link action=showall view=showall_Week time=$next_timestamp}" title="{'Week of'|gettext} {$next_timestamp|format_date:"%B %e, %Y"}">{$next_timestamp|format_date:"%b %e"}</a>&#160;&#160;&raquo;&#160;
		<a class="module-actions calendar_mngmntlink" href="{link action=showall view=showall_Week time=$next_timestamp2}" title="{'Week of'|gettext} {$next_timestamp2|format_date:"%B %e, %Y"}">{$next_timestamp2|format_date:"%b %e"}</a>
        <a class="module-actions" style="float:right;" href="javascript:void(0);" id="J_popup_closeable">{'Go to Date'|gettext}</a>
	</p>
	<dl class="viewweek">
		{foreach from=$days item=items key=ts}
			<dt>
				<strong>
				{if $counts[$ts] != 0}
					<a class="itemtitle calendar_mngmntlink" href="{link action=showall view=Day time=$ts}">{$ts|format_date:"%A, %b %e"}</a>
				{else}
					{$ts|format_date:"%A, %b %e"}
				{/if}
				</strong>
			</dt>
			{assign var=none value=1}
			{foreach from=$items item=item}
				{assign var=none value=0}
				<dd>
                    <a class="itemtitle"
                        {if substr($item->location_data,1,8) != 'calevent'}
                            href="{if $item->location_data != 'event_registration'}{link action=show id=$item->date_id}{else}{link controller=eventregistration action=showByTitle title=$item->title}{/if}"
                        {/if}
                        title="{$item->body|summarize:"html":"para"}">{$item->title}
                     </a>
					{permissions}
                        {if substr($item->location_data,0,3) == 'O:8'}
                            <div class="item-actions">
                                {if $permissions.edit == 1}
                                    {icon action=edit record=$item date_id=$item->date_id title="Edit this Event"|gettext}
                                {/if}
                                {if $permissions.delete == 1}
                                    {if $item->is_recurring == 0}
                                        {icon action=delete record=$item date_id=$item->date_id title="Delete this Event"|gettext}
                                    {else}
                                        {icon action=delete_form class=delete record=$item date_id=$item->date_id title="Delete this Event"|gettext}
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
		fullpath: '{/literal}{$asset_path}js/calendar.js{literal}',
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
        var unixtime = parseInt(d / 1000);
        {/literal} {if ($smarty.const.SEF_URLS == 1)} {literal}
            window.location=eXp.PATH_RELATIVE+'event/showall/view/Week/time/'+unixtime+'/src/{/literal}{$__loc->src}{literal}';
        {/literal} {else} {literal}
            window.location=eXp.PATH_RELATIVE+'index.php?controller=event&action=showall&view=Week&time='+unixtime+'&src={/literal}{$__loc->src}{literal}';
        {/literal} {/if} {literal}
	});

});
{/literal}
{/script}
