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
 
{css unique="cal" link="`$asset_path`css/calendar.css"}

{/css}

<div class="module events list">
	<a class="monthviewlink" href="{link action=showall time=$time}">{'Month View'|gettext}</a>&#160;&#160;|&#160;&#160;<span class="listviewlink">{'List View'|gettext}</span><br />
	<a href="#" onclick="window.open('popup.php?controller=event&src={$__loc->src}&action=showall&view=showall_Monthly+List&template=printerfriendly&time={$time}','printer','title=no,scrollbars=no,width=800,height=600'); return false">{'Printer-friendly'|gettext}</a>
	{br}{br}
	<a class="mngmntlink calendar_mngmntlink" href="{link action=showall view='showall_Monthly List' time=$prev_timestamp}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'left.png'}" title="{'Prev'|gettext}" alt="{'Prev'|gettext}" /></a>
	<strong>{$time|format_date:"%B %Y"}</strong>
	<a class="mngmntlink calendar_mngmntlink" href="{link action=showall view='showall_Monthly List' time=$next_timestamp}"><img class="mngmnt_icon" style="border:none;" src="{$smarty.const.ICON_RELATIVE|cat:'right.png'}" title="{'Next'|gettext}" alt="{'next'|gettext}" /></a>
	{br}{br}
	{foreach from=$days item=items key=ts}
		{if_elements array=$items}
			<div class="sectiontitle">
			{$ts|format_date}
			</div>
			{assign var=none value=1}
			{foreach from=$items item=item}
				{assign var=none value=0}
				<div class="paragraph">
                    <a class="mngmntlink calendar_mngmntlink"
                        {if $item->location_data != null}
                            href="{if $item->location_data != 'event_registration'}{link action=show id=$item->eventdate->id}{else}{link controller=eventregistration action=showByTitle title=$item->title}{/if}"
                        {/if}
                        title="{$item->body|summarize:"html":"para"}">{$item->title}
                    </a>
					{if $item->is_allday == 0}&#160;{$item->eventstart|format_date:$smarty.const.DISPLAY_TIME_FORMAT} - {$item->eventend|format_date:$smarty.const.DISPLAY_TIME_FORMAT}{/if}
					{if $permissions.edit == 1 || $permissions.delete == 1 || $permissions.manage == 1}
						<br />&#160;&#160;&#160;&#160;&#160;&#160;
					{/if}
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
				</div>
				{br}
			{/foreach}
			{if $none == 1}
				<div class="paragraph"><strong>{'No Events'|gettext}</strong></div>
			{/if}
			{br}
		{/if_elements}
	{/foreach}
	{permissions}
		{if $permissions.create == 1}
			<div class="module-actions">
				{icon class="add" action=edit title="Add a New Event"|gettext text="Add an Event"|gettext}
			</div>
		{/if}
	{/permissions}
</div>
