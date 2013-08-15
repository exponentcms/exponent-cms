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

    {$myloc=serialize($__loc)}
	<p class="caption">
		&laquo;&#160;
		<a class="nav module-actions" href="{link action=showall view='showall_Monthly List' time=$prev_timestamp3}" rel="{$prev_timestamp3}" title="{$prev_timestamp3|format_date:"%B %Y"}">{$prev_timestamp3|format_date:"%b"}</a>&#160;&#160;&laquo;&#160;
		<a class="nav module-actions" href="{link action=showall view='showall_Monthly List' time=$prev_timestamp2}" rel="{$prev_timestamp2}" title="{$prev_timestamp2|format_date:"%B %Y"}">{$prev_timestamp2|format_date:"%b"}</a>&#160;&#160;&laquo;&#160;
		<a class="nav module-actions" href="{link action=showall view='showall_Monthly List' time=$prev_timestamp}" rel="{$prev_timestamp}" title="{$prev_timestamp|format_date:"%B %Y"}">{$prev_timestamp|format_date:"%b"}</a>&#160;&#160;&laquo;&#160;&#160;&#160;&#160;&#160;
        <strong>{$time|format_date:"%B %Y"}</strong>&#160;&#160;{printer_friendly_link view='showall_Monthly+List' text=''|gettext}{export_pdf_link view='showall_Monthly+List' text=''|gettext}&#160;&#160;&#160;&#160;&raquo;&#160;&#160;
		<a class="nav module-actions" href="{link action=showall view='showall_Monthly List' time=$next_timestamp}" rel="{$next_timestamp}" title="{$next_timestamp|format_date:"%B %Y"}">{$next_timestamp|format_date:"%b"}</a>&#160;&#160;&raquo;&#160;
		<a class="nav module-actions" href="{link action=showall view='showall_Monthly List' time=$next_timestamp2}" rel="{$next_timestamp2}" title="{$next_timestamp2|format_date:"%B %Y"}">{$next_timestamp2|format_date:"%b"}</a>&#160;&#160;&raquo;&#160;
		<a class="nav module-actions" href="{link action=showall view='showall_Monthly List' time=$next_timestamp3}" rel="{$next_timestamp3}" title="{$next_timestamp3|format_date:"%B %Y"}">{$next_timestamp3|format_date:"%b"}</a>&#160;&#160;&raquo;
	</p>
	<dl class="viewweek">
		{foreach from=$days item=items key=ts}
			{if_elements array=$items}
				<dt>
					<div class="sectiontitle"><strong>
						<a class="itemtitle" href="{link action=showall view=showall_Day time=$ts}">{$ts|format_date:"%A, %b %e"}</a>
					</strong></div>
				</dt>
				<dd>
                    {$none=1}
					{foreach from=$items item=item}
                        {$none=0}
						<div class="paragraph">
                            {if $item->is_cancelled}<span class="cancelled-label">{'This Event Has Been Cancelled!'|gettext}</span>{br}{/if}
							<a class="itemtitle{if $item->is_cancelled} cancelled{/if}{if $config.usecategories && !empty($item->color)} {$item->color}{/if}"
                                {if substr($item->location_data,1,8) != 'calevent'}
                                   href="{if $item->location_data != 'event_registration'}{link action=show date_id=$item->date_id}{else}{link controller=eventregistration action=show title=$item->title}{/if}"
                               {/if}
                               title="{$item->body|summarize:"html":"para"}">{$item->title}
                            </a>
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
                                                {icon action=delete_recurring class=delete record=$item date_id=$item->date_id title="Delete this Event"|gettext}
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
                                {*{$item->body|summarize:"html":"paralinks"}*}
                                {$item->body|summarize:"html":"parahtml"}
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
