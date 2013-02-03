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
 
{css unique="cal" link="`$asset_path`css/calendar.css"}

{/css}

<div class="module events upcoming">
	<div class="module-actions">
		{icon class="monthviewlink" action=showall time=$time text='Calendar View'|gettext}
		{permissions}
			{if $permissions.manage == 1}
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
	</div>
	<h1>
        {ical_link}
        {if $moduletitle && !($config.hidemoduletitle xor $smarty.const.INVERT_HIDE_TITLE)}{$moduletitle}{/if}
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
	<dl class="viewweek">
	{foreach from=$items item=item}
		<dt>
            {if $item->is_cancelled}<span class="cancelled-label">{'This Event Has Been Cancelled!'|gettext}</span>{br}{/if}
			<strong>
                <a class="itemtitle{if $item->is_cancelled} cancelled{/if}{if $config.usecategories && !empty($item->color)} {$item->color}{/if}"
                    {if substr($item->location_data,1,8) != 'calevent'}
                        href="{if $item->location_data != 'event_registration'}{link action=show date_id=$item->date_id}{else}{link controller=eventregistration action=show title=$item->title}{/if}"
                    {/if}
                    {$item->title}
                </a>
            </strong>
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
                            {icon action=copy record=$item date_id=$item->date_id title="Copy this Event"|gettext}
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
		</dt>
		<dd>
            <strong>
				{if $item->is_allday == 1}
					{$item->eventstart|format_date}
				{elseif $item->eventstart != $item->eventend}
					{$item->eventstart|format_date} @ {$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} {'to'|gettext} {$item->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
				{else}
					{$item->eventstart|format_date} @ {$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT}
				{/if}
			</strong>
		</dd>
		<dd>
            {if $config.usebody=='0'}
                {$item->body}
            {elseif $config.usebody==2}
            {else}
                <p>{$item->body|summarize:"html":"paralinks"}</p>
            {/if}
		</dd>
	{foreachelse}
		<dd><em>{'No upcoming events.'|gettext}</em></dd>
	{/foreach}
	</dl>
</div>