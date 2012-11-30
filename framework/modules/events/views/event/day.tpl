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
		<a class="nav module-actions" href="javascript:void(0);" rel="{$prev_timestamp3}" title="{$prev_timestamp3|format_date:"%A, %B %e, %Y"}">{$prev_timestamp3|format_date:"%a"}</a>&#160;&#160;&laquo;&#160;
		<a class="nav module-actions" href="javascript:void(0);" rel="{$prev_timestamp2}" title="{$prev_timestamp2|format_date:"%A, %B %e, %Y"}">{$prev_timestamp2|format_date:"%a"}</a>&#160;&#160;&laquo;&#160;
		<a class="nav module-actions" href="javascript:void(0);" rel="{$prev_timestamp}" title="{$prev_timestamp|format_date:"%A, %B %e, %Y"}">{$prev_timestamp|format_date:"%a"}</a>&#160;&#160;&laquo;&#160;&#160;&#160;&#160;&#160;
        <strong>{$time|format_date:"%A, %B %e, %Y"}</strong>&#160;&#160;&#160;&#160;&#160;&#160;&raquo;&#160;&#160;
		<a class="nav module-actions" href="javascript:void(0);" rel="{$next_timestamp}" title="{$next_timestamp|format_date:"%A, %B %e, %Y"}">{$next_timestamp|format_date:"%a"}</a>&#160;&#160;&raquo;&#160;
		<a class="nav module-actions" href="javascript:void(0);" rel="{$next_timestamp2}" title="{$next_timestamp2|format_date:"%A, %B %e, %Y"}">{$next_timestamp2|format_date:"%a"}</a>&#160;&#160;&raquo;&#160;
		<a class="nav module-actions" href="javascript:void(0);" rel="{$next_timestamp3}" title="{$next_timestamp3|format_date:"%A, %B %e, %Y"}">{$next_timestamp3|format_date:"%a"}</a>
	</p>
	<dl class="viewweek">
        {$count=0}
		{foreach from=$days.$time item=item}
            {$count=1}
			<dt>
				<span class="eventtitle">
                    <a class="itemtitle{if $config.usecategories && !empty($item->color)} {$item->color}{/if}"
                        {if substr($item->location_data,1,8) != 'calevent'}
                            href="{if $item->location_data != 'event_registration'}{link action=show date_id=$item->date_id}{else}{link controller=eventregistration action=showByTitle title=$item->title}{/if}"
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
                                {icon action=copy record=$item date_id=$item->date_id title="Copy this Event"|gettext}
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
