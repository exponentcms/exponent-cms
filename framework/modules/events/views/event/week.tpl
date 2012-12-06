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

    {$myloc=serialize($__loc)}
	<p class="caption">
		<a class="nav module-actions" href="{link action=showall view=showall_Week time=$prev_timestamp2}" rel="{$prev_timestamp2}" title="{'Week of'|gettext} {$prev_timestamp2|format_date:"%B %e, %Y"}">{$prev_timestamp2|format_date:"%b %e"}</a>&#160;&#160;&laquo;&#160;
		<a class="nav module-actions"  href="{link action=showall view=showall_Week time=$prev_timestamp}" rel="{$prev_timestamp}" title="{'Week of'|gettext} {$prev_timestamp|format_date:"%B %e, %Y"}">{$prev_timestamp|format_date:"%b %e"}</a>&#160;&#160;&laquo;&#160;&#160;&#160;&#160;&#160;
        <strong>{'Week of'|gettext} {$time|format_date:"%B %e, %Y"}</strong>&#160;&#160;&#160;&#160;&#160;&#160;&raquo;&#160;&#160;
		<a class="nav module-actions" href="{link action=showall view=showall_Week time=$next_timestamp}" rel="{$next_timestamp}" title="{'Week of'|gettext} {$next_timestamp|format_date:"%B %e, %Y"}">{$next_timestamp|format_date:"%b %e"}</a>&#160;&#160;&raquo;&#160;
		<a class="nav module-actions" href="{link action=showall view=showall_Week time=$next_timestamp2}" rel="{$next_timestamp2}" title="{'Week of'|gettext} {$next_timestamp2|format_date:"%B %e, %Y"}">{$next_timestamp2|format_date:"%b %e"}</a>
	</p>
	<dl class="viewweek">
		{foreach from=$days item=items key=ts}
			<dt>
				<strong>
				{if count($items) != 0}
					<a class="itemtitle calendar_mngmntlink" href="{link action=showall view=showall_Day time=$ts}">{$ts|format_date:"%A, %b %e"}</a>
				{else}
					{$ts|format_date:"%A, %b %e"}
				{/if}
				</strong>
			</dt>
            {$none=1}
			{foreach from=$items item=item}
                {$none=0}
				<dd>
                    <a class="itemtitle{if $config.usecategories && !empty($item->color)} class="{$item->color}"{/if}"
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
