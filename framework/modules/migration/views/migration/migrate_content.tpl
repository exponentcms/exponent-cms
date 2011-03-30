{*
 * Copyright (c) 2004-2011 OIC Group, Inc.
 * Written and Designed by Adam Kessler
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

{css unique="migcont" corecss="tables"}

{/css}

<div class="module migration migrate_content">
	<table border="0" cellspacing="0" cellpadding="0" class="exp-skin-table">
		<thead>
			<tr>
				<th>
					{"Content Migration Report"|gettext}
					{if $msg.clearedcontent}
						{br} After clearing the database of content:
					{/if}
				</th>
			</tr>
		</thead>
		<tbody>
			<tr><td>Migrated {if $msg.locationref}{$msg.locationref} total locations and {/if}{if $msg.container}{$msg.container} total containers{/if} which included:</td></tr>
			{foreach from=$msg.migrated item=val key=key}
				<tr class="{cycle values="odd,even"}">
					<td>
						{if $key == $val.name}
							<strong>{$val.count}</strong> record{if $val.count!=1}s{/if} from <strong>{$key}</strong> {if $val.count!=1}have{else}has{/if} been migrated as is</strong>
						{else}
							<strong>{$val.count}</strong> record{if $val.count!=1}s{/if} from <strong>{$key}</strong> {if $val.count!=1}have{else}has{/if} been migrated to <strong>{$val.name}</strong>
						{/if}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
</div>