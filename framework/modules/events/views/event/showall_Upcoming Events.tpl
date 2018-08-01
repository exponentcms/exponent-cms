{*
 * Copyright (c) 2004-2018 OIC Group, Inc.
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

{css unique="cal" link="`$asset_path`css/calendar.css"}

{/css}

<div class="module events upcoming">
	<div class="module-actions">
        {if !$config.disable_links}
    		{icon class="monthviewlink" action=showall time=$time text='Calendar View'|gettext nofollow=1}
        {/if}
		{permissions}
			{if $permissions.manage}
                {if !bs()}
                    {nbsp count=2}|{nbsp count=2}
                {/if}
                {icon class="adminviewlink" action=showall view=showall_Administration time=$time text='Administration View'|gettext nofollow=1}
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
			{/if}
		{/permissions}
        {printer_friendly_link text='Printer-friendly'|gettext prepend='&#160;&#160;|&#160;&#160;'|not_bs}
        {export_pdf_link prepend='&#160;&#160;|&#160;&#160;'|not_bs}
	</div>
	<{$config.heading_level|default:'h1'}>
        {ical_link}
        {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}{/if}
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
		</div>
	{/permissions}
	<dl class="viewweek">
	{foreach from=$items item=item}
        <div class="vevent">
		<dt>
            {if $item->is_cancelled}<span class="cancelled-label">{'This Event Has Been Cancelled!'|gettext}</span>{br}{/if}
			<strong>
                <a class="url itemtitle{if $item->is_cancelled} cancelled{/if}{if !empty($item->color)} {$item->color}{/if}"
                    {if substr($item->location_data,1,8) != 'calevent'}
                        href="{if $item->location_data != 'event_registration'}{link action=show date_id=$item->date_id}{else}{link controller=eventregistration action=show title=$item->title}{/if}"
                    {/if}
                    ><div><span class="summary">{$item->title}</span></div>
                </a>
            </strong>
			{permissions}
                {if substr($item->location_data,0,3) == 'O:8'}
                    <div class="item-actions">
                        {if $permissions.edit || ($permissions.create && $item->poster == $user->id)}
                            {if $myloc != $item->location_data}
                                {if $permissions.manage}
                                    {icon action=merge id=$item->id title="Merge Aggregated Content"|gettext}
                                {else}
                                    {icon img='arrow_merge.png' title="Merged Content"|gettext}
                                {/if}
                            {/if}
                            {icon action=edit record=$item date_id=$item->date_id title="Edit this Event"|gettext}
                            {icon action=copy record=$item date_id=$item->date_id title="Copy this Event"|gettext}
                        {/if}
                        {if $permissions.delete || ($permissions.create && $item->poster == $user->id)}
                            {if $item->is_recurring == 0}
                                {icon action=delete record=$item date_id=$item->date_id title="Delete this Event"|gettext}
                            {else}
                                {icon action=delete_recurring class=delete record=$item date_id=$item->date_id title="Delete this Event"|gettext}
                            {/if}
                        {/if}
                    </div>
                {/if}
			{/permissions}
		</dt>
		<dd>
            <strong>
				{if $item->is_allday == 1}
                    <span class="dtstart">{$item->eventstart|format_date}<span class="value-title" title="{date('c',$item->eventstart)}"></span></span>
				{elseif $item->eventstart != $item->eventend}
                    <span class="dtstart">{$item->eventstart|format_date} @ {$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}<span class="value-title" title="{date('c',$item->eventstart)}"></span></span>
                    {'to'|gettext} {$item->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}<span class="duration"><span class="value-title" title="{expDateTime::duration($item->eventstart,$item->eventend,true)}"></span></span>
				{else}
                    <span class="dtstart">{$item->eventstart|format_date} @ {$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}<span class="value-title" title="{date('c',$item->eventstart)}"></span></span>
				{/if}
			</strong>
            {$endd = end($item->eventdate)}
            {$end = $endd->date}
            {if $end > $item->eventend && ($end <= $item->eventend + 31*24*60*60+1)}
                <span style="font-style: italic;color: grey;">
                    ({'thru'|gettext} {$end|format_date:"%b"} {$end|format_date:"%e"}{date("S",mktime(0,0,0,0,$end|format_date:"%e",0))})
                </span>
            {/if}
		</dd>
		<dd>
            {if !empty($item->expFile[0]->url)}
                <div class="image photo">
                    {img file_id=$item->expFile[0]->id title="`$item->title`" h=48}
                </div>
            {/if}
            <span class="description">
            {if $config.usebody=='0'}
                {$item->body}
            {elseif $config.usebody==3}
                {$item->body|summarize:"html":"parapaged"}
            {elseif $config.usebody==2}
            {else}
                {*<p>{$item->body|summarize:"html":"paralinks"}</p>*}
                <p>{$item->body|summarize:"html":"parahtml"}</p>
            {/if}
            </span>
            <span class="hide">
                {'Location'|gettext}:
                <span class="location">
                    {$smarty.const.ORGANIZATION_NAME}
                </span>
                {if !empty($item->event->expCat[0]->title)}<span class="category">{$item->event->expCat[0]->title}</span>{/if}
            </span>
            {clear}
		</dd>
        </div>
	{foreachelse}
		<dd><em>{'No upcoming events.'|gettext}</em></dd>
	{/foreach}
	</dl>
</div>